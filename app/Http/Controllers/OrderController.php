<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /** Waktu transaksi dari aplikasi kasir disimpan sebagai string ISO-8601. */
    private const TX_TIME = "STR_TO_DATE(transaction_time, '%Y-%m-%dT%H:%i:%s')";

    public function index(Request $request)
    {
        $start_date = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $end_date = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        if ($start_date > $end_date) {
            return redirect()->route('order_reports.index')->with('error', 'Tanggal mulai tidak boleh setelah tanggal akhir.');
        }

        $startDateTime = Carbon::parse($start_date)->startOfDay()->toDateTimeString();
        $endDateTime = Carbon::parse($end_date)->endOfDay()->toDateTimeString();

        $method = strtolower((string) $request->input('method'));
        $search = trim((string) $request->input('q'));

        $base = Order::query()->whereRaw(self::TX_TIME . ' BETWEEN ? AND ?', [$startDateTime, $endDateTime]);

        // Ringkasan dihitung dalam satu query agregat (sebelumnya 13 query terpisah).
        $agg = (clone $base)->selectRaw("
            COUNT(*) as orders,
            COALESCE(SUM(payment_amount),0) as total_revenue,
            COALESCE(SUM(discount_amount),0) as total_discount,
            COALESCE(SUM(tax),0) as total_tax,
            COALESCE(SUM(sub_total),0) as total_subtotal,
            COALESCE(SUM(service_charge),0) as total_service_charge,
            COALESCE(SUM(total_item),0) as total_items,
            COALESCE(SUM(CASE WHEN LOWER(payment_method) = 'cash' THEN payment_amount ELSE 0 END),0) as total_cash,
            COALESCE(SUM(CASE WHEN LOWER(payment_method) = 'transfer' THEN payment_amount ELSE 0 END),0) as total_transfer
        ")->first();

        $summary = [
            'orders' => (int) $agg->orders,
            'total_items' => (int) $agg->total_items,
            'total_revenue' => (float) $agg->total_revenue,
            'total_discount' => (float) $agg->total_discount,
            'total_tax' => (float) $agg->total_tax,
            'total_subtotal' => (float) $agg->total_subtotal,
            'total_service_charge' => (float) $agg->total_service_charge,
            'total_cash' => (float) $agg->total_cash,
            'total_transfer' => (float) $agg->total_transfer,
            // Rumus "Fulus Bersih" sama seperti sebelumnya.
            'total' => (float) $agg->total_subtotal - (float) $agg->total_discount - (float) $agg->total_tax + (float) $agg->total_service_charge,
        ];
        $summary['avg'] = $summary['orders'] > 0 ? $summary['total'] / $summary['orders'] : 0;

        // Tren harian dalam rentang terpilih.
        $daily = (clone $base)
            ->selectRaw('DATE(' . self::TX_TIME . ') as d, COUNT(*) as orders, COALESCE(SUM(sub_total),0) - COALESCE(SUM(discount_amount),0) - COALESCE(SUM(tax),0) + COALESCE(SUM(service_charge),0) as net')
            ->groupBy(DB::raw('DATE(' . self::TX_TIME . ')'))
            ->orderBy('d')
            ->get()
            ->keyBy('d');

        $chart = ['labels' => [], 'net' => [], 'orders' => []];
        $cursor = Carbon::parse($start_date);
        $last = Carbon::parse($end_date);
        if ($cursor->diffInDays($last) <= 92) {
            for (; $cursor->lte($last); $cursor->addDay()) {
                $row = $daily->get($cursor->toDateString());
                $chart['labels'][] = $cursor->translatedFormat('d M');
                $chart['net'][] = (float) ($row->net ?? 0);
                $chart['orders'][] = (int) ($row->orders ?? 0);
            }
        } else {
            foreach ($daily as $d => $row) {
                $chart['labels'][] = Carbon::parse($d)->translatedFormat('d M y');
                $chart['net'][] = (float) $row->net;
                $chart['orders'][] = (int) $row->orders;
            }
        }

        $methods = (clone $base)
            ->selectRaw('LOWER(payment_method) as method')
            ->distinct()
            ->pluck('method');

        $orders = (clone $base)
            ->when($method !== '', fn ($q) => $q->whereRaw('LOWER(payment_method) = ?', [$method]))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($w) use ($search) {
                    $w->where('customer_name', 'like', "%{$search}%")
                        ->orWhere('nama_kasir', 'like', "%{$search}%")
                        ->orWhere('id', $search);
                });
            })
            ->orderByRaw(self::TX_TIME . ' DESC')
            ->paginate(25)
            ->withQueryString();

        $chartSummary = $chart;

        return view('pages.order_reports.index', compact(
            'orders', 'summary', 'chartSummary', 'start_date', 'end_date', 'methods', 'method', 'search'
        ));
    }

    /** Ringkasan JSON (dipertahankan untuk kompatibilitas). */
    public function summary(Request $request)
    {
        $query = Order::query();
        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('created_at', [$request->input('start_date'), $request->input('end_date')]);
        }

        $row = $query->selectRaw('
            COALESCE(SUM(payment_amount),0) as total_revenue,
            COALESCE(SUM(discount_amount),0) as total_discount,
            COALESCE(SUM(tax),0) as total_tax,
            COALESCE(SUM(service_charge),0) as total_service_charge,
            COALESCE(SUM(sub_total),0) as total_subtotal
        ')->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_revenue' => $row->total_revenue,
                'total_discount' => $row->total_discount,
                'total_tax' => $row->total_tax,
                'total_subtotal' => $row->total_subtotal,
                'total_service_charge' => $row->total_service_charge,
                'total' => $row->total_subtotal - $row->total_discount - $row->total_tax + $row->total_service_charge,
            ],
        ], 200);
    }

    /** Detail satu order untuk modal di Laporan Penjualan. */
    public function show($id)
    {
        $order = Order::with('orderItems.product:id,name')->findOrFail($id);

        return response()->json([
            'order' => $order,
            'transaction_label' => $this->displayTime($order->transaction_time),
            'items' => $order->orderItems->map(function ($item) {
                // product_name disimpan saat transaksi; fallback ke nama produk saat ini.
                $name = $item->product_name ?: optional($item->product)->name ?: 'Produk dihapus';
                $lineTotal = $item->quantity * $item->price;

                return [
                    'product_name' => $name,
                    'quantity' => $item->quantity,
                    'price' => number_format($item->price, 2),
                    'total' => number_format($lineTotal, 2),
                    'price_raw' => (float) $item->price,
                    'total_raw' => (float) $lineTotal,
                ];
            })->values(),
        ]);
    }

    /** Konvensi tampilan lama: waktu transaksi + 2 jam. */
    private function displayTime(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value)->addHours(2)->translatedFormat('d M Y, H:i');
        } catch (\Throwable $e) {
            return $value;
        }
    }
}
