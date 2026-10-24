<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Rumus "penjualan bersih" disamakan dengan Laporan Penjualan & Analisa Keuangan:
     * subtotal - diskon - pajak + service charge.
     */
    private const NET = 'COALESCE(SUM(sub_total),0) - COALESCE(SUM(discount_amount),0) - COALESCE(SUM(tax),0) + COALESCE(SUM(service_charge),0)';

    public function index()
    {
        $user = auth()->user();
        $reminder = $this->cloudReminder();

        // Akun non-admin (staff/kasir) tidak melihat angka keuangan.
        if ($user->role !== 'admin') {
            return view('pages.dashboard', [
                'isAdmin' => false,
                'reminder' => $reminder,
            ]);
        }

        $now = Carbon::now();
        $today = $now->copy()->startOfDay();

        $todayStats = $this->periodStats($today, $now->copy()->endOfDay());
        $yesterdayStats = $this->periodStats($today->copy()->subDay(), $today->copy()->subDay()->endOfDay());

        $monthStart = $now->copy()->startOfMonth();
        $monthStats = $this->periodStats($monthStart, $now->copy()->endOfDay());

        // Pembanding: periode yang sama di bulan lalu (tgl 1 s/d tanggal hari ini).
        $prevMonthStart = $monthStart->copy()->subMonthNoOverflow();
        $prevMonthEnd = $prevMonthStart->copy()->addDays($now->day - 1)->endOfDay();
        if ($prevMonthEnd->greaterThan($prevMonthStart->copy()->endOfMonth())) {
            $prevMonthEnd = $prevMonthStart->copy()->endOfMonth();
        }
        $prevMonthStats = $this->periodStats($prevMonthStart, $prevMonthEnd);

        // Tren 14 hari terakhir.
        $trendStart = $today->copy()->subDays(13);
        $trendRows = Order::query()
            ->selectRaw('DATE(created_at) as d, ' . self::NET . ' as net, COUNT(*) as orders')
            ->whereBetween('created_at', [$trendStart, $now->copy()->endOfDay()])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()
            ->keyBy('d');

        $trend = ['labels' => [], 'net' => [], 'orders' => []];
        for ($i = 0; $i < 14; $i++) {
            $day = $trendStart->copy()->addDays($i);
            $row = $trendRows->get($day->toDateString());
            $trend['labels'][] = $day->translatedFormat('d M');
            $trend['net'][] = (float) ($row->net ?? 0);
            $trend['orders'][] = (int) ($row->orders ?? 0);
        }

        // Metode pembayaran bulan ini.
        $payments = Order::query()
            ->selectRaw('LOWER(payment_method) as method, COUNT(*) as orders, ' . self::NET . ' as net')
            ->whereBetween('created_at', [$monthStart, $now->copy()->endOfDay()])
            ->groupBy(DB::raw('LOWER(payment_method)'))
            ->orderByDesc('net')
            ->get();

        // Produk terlaris 30 hari (nama dari order_items bila produk sudah dihapus).
        $topProducts = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.created_at', '>=', $today->copy()->subDays(29))
            ->groupBy('order_items.product_id')
            ->selectRaw('order_items.product_id, COALESCE(MAX(products.name), MAX(order_items.product_name), \'Produk\') as name, SUM(order_items.quantity) as qty, SUM(order_items.quantity * order_items.price) as revenue')
            ->orderByDesc('qty')
            ->limit(6)
            ->get();

        $recentOrders = Order::query()
            ->latest('id')
            ->limit(7)
            ->get(['id', 'customer_name', 'total', 'payment_method', 'total_item', 'nama_kasir', 'created_at', 'transaction_time']);

        // Persediaan menipis: sisa <= 20% dari pembelian awal.
        $lowStock = Inventory::query()
            ->where('saldo_awal', '>', 0)
            ->whereRaw('quantity <= saldo_awal * 0.2')
            ->orderByRaw('quantity / saldo_awal asc')
            ->latest('tanggal_masuk')
            ->limit(6)
            ->get(['id', 'nama_bahan', 'quantity', 'saldo_awal', 'satuan', 'tanggal_masuk']);

        $counts = [
            'products' => DB::table('products')->where('status', 1)->count(),
            'categories' => DB::table('categories')->count(),
            'out_of_stock' => Inventory::where('quantity', '<=', 0)->count(),
        ];

        return view('pages.dashboard', [
            'isAdmin' => true,
            'reminder' => $reminder,
            'today' => $todayStats,
            'yesterday' => $yesterdayStats,
            'month' => $monthStats,
            'prevMonth' => $prevMonthStats,
            'trend' => $trend,
            'payments' => $payments,
            'topProducts' => $topProducts,
            'recentOrders' => $recentOrders,
            'lowStock' => $lowStock,
            'counts' => $counts,
        ]);
    }

    private function periodStats(Carbon $from, Carbon $to): array
    {
        $row = Order::query()
            ->selectRaw(self::NET . ' as net, COUNT(*) as orders, COALESCE(SUM(total_item),0) as items')
            ->whereBetween('created_at', [$from, $to])
            ->first();

        $net = (float) ($row->net ?? 0);
        $orders = (int) ($row->orders ?? 0);

        return [
            'net' => $net,
            'orders' => $orders,
            'items' => (int) ($row->items ?? 0),
            'avg' => $orders > 0 ? $net / $orders : 0,
        ];
    }

    /** Pengingat perpanjangan cloud server tiap 23 Januari. */
    private function cloudReminder(): array
    {
        $now = Carbon::now()->startOfDay();
        $due = Carbon::create($now->year, 1, 23)->startOfDay();
        if ($due->lessThan($now)) {
            $due->addYear();
        }

        return [
            'due' => $due,
            'days' => $now->diffInDays($due),
        ];
    }
}
