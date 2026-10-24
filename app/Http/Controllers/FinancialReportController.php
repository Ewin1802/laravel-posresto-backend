<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Inventory_out;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        $start_date = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $end_date = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        if ($start_date > $end_date) {
            return redirect()->route('financial.report')->with('error', 'Tanggal mulai tidak boleh setelah tanggal akhir.');
        }

        $startDate = Carbon::parse($start_date)->startOfDay();
        $endDate = Carbon::parse($end_date)->endOfDay();

        // Belanja persediaan = saldo awal x harga satuan (rumus lama, dihitung di database).
        $totalPersediaan = (float) Inventory::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('COALESCE(SUM(saldo_awal * amount),0) as v')
            ->value('v');

        $orderAgg = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('COUNT(*) as orders, COALESCE(SUM(sub_total),0) as sub_total, COALESCE(SUM(discount_amount),0) as discount, COALESCE(SUM(tax),0) as tax, COALESCE(SUM(service_charge),0) as service')
            ->first();

        $totalKeuangan = (float) $orderAgg->sub_total - (float) $orderAgg->discount - (float) $orderAgg->tax + (float) $orderAgg->service;

        // Bahan yang benar-benar terpakai (persediaan keluar) dalam periode.
        $bahanTerpakai = (float) Inventory_out::query()
            ->join('inventories', 'inventories.id', '=', 'inventory_outs.inventory_id')
            ->whereBetween('inventory_outs.created_at', [$startDate, $endDate])
            ->selectRaw('COALESCE(SUM(inventory_outs.quantity_out * inventories.amount),0) as v')
            ->value('v');

        $selisih = $totalKeuangan - $totalPersediaan;
        $margin = $totalKeuangan > 0 ? ($selisih / $totalKeuangan) * 100 : null;

        // Arus mingguan / harian untuk grafik.
        $days = $startDate->diffInDays($endDate) + 1;
        $groupExpr = $days > 62 ? "DATE_FORMAT(created_at, '%Y-%m')" : 'DATE(created_at)';

        $salesSeries = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("{$groupExpr} as k, COALESCE(SUM(sub_total),0) - COALESCE(SUM(discount_amount),0) - COALESCE(SUM(tax),0) + COALESCE(SUM(service_charge),0) as v")
            ->groupBy(DB::raw($groupExpr))
            ->pluck('v', 'k');

        $spendSeries = Inventory::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("{$groupExpr} as k, COALESCE(SUM(saldo_awal * amount),0) as v")
            ->groupBy(DB::raw($groupExpr))
            ->pluck('v', 'k');

        $series = ['labels' => [], 'sales' => [], 'spend' => []];
        $cursor = $startDate->copy();
        while ($cursor->lte($endDate)) {
            $key = $days > 62 ? $cursor->format('Y-m') : $cursor->toDateString();
            $series['labels'][] = $days > 62 ? $cursor->translatedFormat('M Y') : $cursor->translatedFormat('d M');
            $series['sales'][] = (float) ($salesSeries[$key] ?? 0);
            $series['spend'][] = (float) ($spendSeries[$key] ?? 0);
            $days > 62 ? $cursor->addMonthNoOverflow()->startOfMonth() : $cursor->addDay();
        }

        return view('pages.financial.index', compact(
            'startDate', 'endDate', 'start_date', 'end_date',
            'totalPersediaan', 'totalKeuangan', 'bahanTerpakai', 'selisih', 'margin', 'orderAgg', 'series'
        ));
    }
}
