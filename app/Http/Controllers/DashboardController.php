<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Support\CloudReminder;
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

        // Pengeluaran (pembanding laba).
        $monthExpense = (float) Expense::whereBetween('expense_date', [$monthStart->toDateString(), $now->toDateString()])->sum('amount');
        $prevMonthExpense = (float) Expense::whereBetween('expense_date', [$prevMonthStart->toDateString(), $prevMonthEnd->toDateString()])->sum('amount');
        $todayExpense = (float) Expense::whereDate('expense_date', $now->toDateString())->sum('amount');

        $expenseByCategory = Expense::whereBetween('expense_date', [$monthStart->toDateString(), $now->toDateString()])
            ->selectRaw('category, COALESCE(SUM(amount),0) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $recentExpenses = Expense::query()
            ->orderByDesc('expense_date')->orderByDesc('id')
            ->limit(5)
            ->get(['id', 'category', 'description', 'amount', 'expense_date']);

        $counts = [
            'products' => DB::table('products')->where('status', 1)->count(),
            'categories' => DB::table('categories')->count(),
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
            'monthExpense' => $monthExpense,
            'prevMonthExpense' => $prevMonthExpense,
            'todayExpense' => $todayExpense,
            'expenseByCategory' => $expenseByCategory,
            'recentExpenses' => $recentExpenses,
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

    /** Pengingat perpanjangan cloud server (pengaturan di App\Support\CloudReminder). */
    private function cloudReminder(): array
    {
        return CloudReminder::info();
    }
}
