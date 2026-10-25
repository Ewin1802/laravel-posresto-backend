<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialReportController extends Controller
{
    /** Rumus penjualan bersih sama seperti Laporan penjualan: subtotal - diskon - pajak + service. */
    private const NET = 'COALESCE(SUM(sub_total),0) - COALESCE(SUM(discount_amount),0) - COALESCE(SUM(tax),0) + COALESCE(SUM(service_charge),0)';

    public function index(Request $request)
    {
        $start_date = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $end_date = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        if ($start_date > $end_date) {
            return redirect()->route('financial.report')->with('error', 'Tanggal mulai tidak boleh setelah tanggal akhir.');
        }

        $startDate = Carbon::parse($start_date)->startOfDay();
        $endDate = Carbon::parse($end_date)->endOfDay();

        // ---- Penjualan (orders) ----
        $orderAgg = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('COUNT(*) as orders, COALESCE(SUM(sub_total),0) as sub_total, COALESCE(SUM(discount_amount),0) as discount, COALESCE(SUM(tax),0) as tax, COALESCE(SUM(service_charge),0) as service')
            ->first();

        $totalKeuangan = (float) $orderAgg->sub_total - (float) $orderAgg->discount - (float) $orderAgg->tax + (float) $orderAgg->service;

        // ---- Pengeluaran (expenses) — pembanding utama ----
        $expenseByCategory = Expense::whereBetween('expense_date', [$start_date, $end_date])
            ->selectRaw('category, COUNT(*) as items, COALESCE(SUM(amount),0) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $totalPengeluaran = (float) $expenseByCategory->sum('total');

        $selisih = $totalKeuangan - $totalPengeluaran;
        $margin = $totalKeuangan > 0 ? ($selisih / $totalKeuangan) * 100 : null;

        // ---- Periode sebelumnya dengan panjang sama, untuk pembanding tren ----
        $days = $startDate->diffInDays($endDate) + 1;
        $prevEnd = $startDate->copy()->subDay()->endOfDay();
        $prevStart = $prevEnd->copy()->subDays($days - 1)->startOfDay();
        $prevSales = (float) Order::whereBetween('created_at', [$prevStart, $prevEnd])->selectRaw(self::NET . ' as v')->value('v');
        $prevExpense = (float) Expense::whereBetween('expense_date', [$prevStart->toDateString(), $prevEnd->toDateString()])->sum('amount');
        $prevProfit = $prevSales - $prevExpense;

        // ---- Seri harian (≤ 62 hari) atau bulanan ----
        $monthly = $days > 62;
        $orderKey = $monthly ? "DATE_FORMAT(created_at, '%Y-%m')" : 'DATE(created_at)';
        $expenseKey = $monthly ? "DATE_FORMAT(expense_date, '%Y-%m')" : 'DATE(expense_date)';

        $salesSeries = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("{$orderKey} as k, " . self::NET . ' as v')
            ->groupBy(DB::raw($orderKey))
            ->pluck('v', 'k');

        $expenseSeries = Expense::whereBetween('expense_date', [$start_date, $end_date])
            ->selectRaw("{$expenseKey} as k, COALESCE(SUM(amount),0) as v")
            ->groupBy(DB::raw($expenseKey))
            ->pluck('v', 'k');

        $series = ['labels' => [], 'sales' => [], 'spend' => [], 'profit' => []];
        $cursor = $startDate->copy();
        while ($cursor->lte($endDate)) {
            $key = $monthly ? $cursor->format('Y-m') : $cursor->toDateString();
            $s = (float) ($salesSeries[$key] ?? 0);
            $e = (float) ($expenseSeries[$key] ?? 0);
            $series['labels'][] = $monthly ? $cursor->translatedFormat('M Y') : $cursor->translatedFormat('d M');
            $series['sales'][] = $s;
            $series['spend'][] = $e;
            $series['profit'][] = $s - $e;
            $monthly ? $cursor->addMonthNoOverflow()->startOfMonth() : $cursor->addDay();
        }

        // Akumulasi laba berjalan (untuk garis kumulatif).
        $running = 0;
        $series['cumulative'] = array_map(function ($p) use (&$running) { return $running += $p; }, $series['profit']);

        return view('pages.financial.index', compact(
            'startDate', 'endDate', 'start_date', 'end_date', 'monthly',
            'totalKeuangan', 'totalPengeluaran', 'expenseByCategory',
            'selisih', 'margin', 'orderAgg', 'series',
            'prevSales', 'prevExpense', 'prevProfit', 'prevStart', 'prevEnd'
        ));
    }
}
