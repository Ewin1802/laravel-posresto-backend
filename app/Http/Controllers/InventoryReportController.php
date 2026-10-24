<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InventoryReportController extends Controller
{
    public function index(Request $request)
    {
        $start_date = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $end_date = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        if ($start_date > $end_date) {
            return redirect()->route('inventory.reports')->with('error', 'Tanggal mulai tidak boleh setelah tanggal akhir.');
        }

        $startDateTime = Carbon::parse($start_date)->startOfDay();
        $endDateTime = Carbon::parse($end_date)->endOfDay();

        $base = Inventory::query()->whereBetween('tanggal_masuk', [$startDateTime, $endDateTime]);

        // Setiap baris inventories adalah satu batch pembelian.
        $inventoryReport = (clone $base)
            ->select('id', 'bahan_id', 'nama_bahan', 'satuan', 'tanggal_masuk', 'supplier', 'saldo_awal', 'quantity', 'amount as harga_satuan')
            ->with([
                'bahan:id,nama_bahan,satuan',
                'inventoryOut' => function ($query) use ($startDateTime, $endDateTime) {
                    $query->whereBetween('created_at', [$startDateTime, $endDateTime])->orderBy('created_at');
                },
            ])
            ->orderBy('nama_bahan')
            ->orderBy('tanggal_masuk')
            ->paginate(15)
            ->withQueryString();

        $totals = (clone $base)
            ->selectRaw('COALESCE(SUM(saldo_awal * amount),0) as masuk, COALESCE(SUM(quantity * amount),0) as sisa, COUNT(*) as batches')
            ->first();

        $keluar = \App\Models\Inventory_out::query()
            ->join('inventories', 'inventories.id', '=', 'inventory_outs.inventory_id')
            ->whereBetween('inventories.tanggal_masuk', [$startDateTime, $endDateTime])
            ->whereBetween('inventory_outs.created_at', [$startDateTime, $endDateTime])
            ->selectRaw('COALESCE(SUM(inventory_outs.quantity_out * inventories.amount),0) as v')
            ->value('v');

        $totals->keluar = (float) $keluar;

        return view('pages.inventory_reports.index', compact('inventoryReport', 'start_date', 'end_date', 'totals'));
    }
}
