<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderItemController extends Controller
{
    public function index(Request $request)
    {
        $start_date = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $end_date = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        if ($start_date > $end_date) {
            return redirect()->route('top.products')->with('error', 'Tanggal mulai tidak boleh setelah tanggal akhir.');
        }

        $startDateTime = Carbon::parse($start_date)->startOfDay();
        $endDateTime = Carbon::parse($end_date)->endOfDay();

        // Nama diambil dari order_items.product_name bila produk sudah dihapus,
        // sehingga laporan tidak error seperti sebelumnya ($item->product->name null).
        $base = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->whereBetween('orders.created_at', [$startDateTime, $endDateTime]);

        $topProducts = (clone $base)
            ->groupBy('order_items.product_id')
            ->selectRaw("
                order_items.product_id,
                COALESCE(MAX(products.name), MAX(order_items.product_name), 'Produk dihapus') as name,
                MAX(products.image) as image,
                MAX(categories.name) as category_name,
                SUM(order_items.quantity) as total_quantity,
                SUM(order_items.quantity * order_items.price) as revenue,
                COUNT(DISTINCT order_items.order_id) as order_count
            ")
            ->orderByDesc('total_quantity')
            ->paginate(15)
            ->withQueryString();

        $totals = (clone $base)
            ->selectRaw('COALESCE(SUM(order_items.quantity),0) as qty, COALESCE(SUM(order_items.quantity * order_items.price),0) as revenue, COUNT(DISTINCT order_items.product_id) as products')
            ->first();

        // Grafik: 10 teratas pada rentang ini (tidak bergantung halaman paginasi).
        $chartRows = (clone $base)
            ->groupBy('order_items.product_id')
            ->selectRaw("COALESCE(MAX(products.name), MAX(order_items.product_name), 'Produk dihapus') as name, SUM(order_items.quantity) as qty")
            ->orderByDesc('qty')
            ->limit(10)
            ->get();

        $chartData = [
            'labels' => $chartRows->pluck('name'),
            'data' => $chartRows->pluck('qty')->map(fn ($v) => (int) $v),
        ];

        return view('pages.order_item_reports.index', compact('topProducts', 'start_date', 'end_date', 'chartData', 'totals'));
    }
}
