<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class OrderController extends Controller
{


    // public function index(Request $request)
    // {
    //     // Ambil input tanggal dari request
    //     $start_date = $request->input('start_date');
    //     $end_date = $request->input('end_date');

    //     // Mulai query dengan model Order
    //     $query = Order::query();

    //     // Filter berdasarkan tanggal jika input tanggal tersedia
    //     // if ($start_date && $end_date) {
    //     //     $query->whereBetween('created_at', [
    //     //         $start_date . ' 00:00:00',
    //     //         $end_date . ' 23:59:59',
    //     //     ]);
    //     // }
    //     if ($start_date && $end_date) {
    //         $query->whereRaw("STR_TO_DATE(transaction_time, '%Y-%m-%dT%H:%i:%s') BETWEEN ? AND ?", [
    //             $start_date . ' 00:00:00',
    //             $end_date . ' 23:59:59',
    //         ]);
    //     }

    //     // Paginasi data
    //     $orders = $query->paginate(10);

    //     // Perhitungan ringkasan data berdasarkan query yang sama
    //     $summary = [
    //         'total_revenue' => $query->sum('payment_amount'),
    //         'total_discount' => $query->sum('discount_amount'),
    //         'total_tax' => $query->sum('tax'),
    //         'total_subtotal' => $query->sum('sub_total'),
    //         'total_service_charge' => $query->sum('service_charge'),
    //         'total' => $query->sum('sub_total') - $query->sum('discount_amount') - $query->sum('tax') + $query->sum('service_charge'),
    //     ];

    //     // Tampilkan halaman dengan data awal
    //     return view('pages.order_reports.index', compact('orders', 'summary', 'start_date', 'end_date'));
    // }

    // public function index(Request $request)
    // {
    //     // Ambil input tanggal dari request
    //     $start_date = $request->input('start_date');
    //     $end_date = $request->input('end_date');

    //     // Mulai query dengan model Order
    //     $query = Order::query();

    //     // Jika tanggal difilter, gunakan filter `whereRaw`
    //     if ($start_date && $end_date) {
    //         $query->whereRaw("STR_TO_DATE(transaction_time, '%Y-%m-%dT%H:%i:%s') BETWEEN ? AND ?", [
    //             $start_date . ' 00:00:00',
    //             $end_date . ' 23:59:59',
    //         ]);

    //         // Ambil seluruh data tanpa paginasi
    //         $orders = $query->get();
    //     } else {
    //         // Jika tidak ada filter tanggal, tampilkan daftar kosong
    //         $orders = collect(); // Koleksi kosong
    //     }

    //     // Perhitungan ringkasan data berdasarkan query
    //     $summary = [
    //         'total_revenue' => $query->sum('payment_amount'),
    //         'total_discount' => $query->sum('discount_amount'),
    //         'total_tax' => $query->sum('tax'),
    //         'total_subtotal' => $query->sum('sub_total'),
    //         'total_service_charge' => $query->sum('service_charge'),
    //         'total' => $query->sum('sub_total') - $query->sum('discount_amount') - $query->sum('tax') + $query->sum('service_charge'),
    //     ];

    //     // Tampilkan halaman dengan data awal
    //     return view('pages.order_reports.index', compact('orders', 'summary', 'start_date', 'end_date'));
    // }


    public function index(Request $request)
    {
        // Ambil input tanggal dari request atau default ke bulan ini
        $start_date = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $end_date = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Validasi tanggal
        if ($start_date > $end_date) {
            return redirect()->back()->with('error', 'Tanggal mulai tidak bisa lebih besar dari tanggal akhir.');
        }

        // Konversi tanggal agar mencakup seluruh hari (00:00:00 - 23:59:59)
        $startDateTime = Carbon::parse($start_date)->startOfDay()->toDateTimeString(); // 2025-02-06 00:00:00
        $endDateTime = Carbon::parse($end_date)->endOfDay()->toDateTimeString(); // 2025-02-06 23:59:59

        // Query dengan konversi STR_TO_DATE agar cocok dengan format ISO 8601
        $query = Order::whereRaw("STR_TO_DATE(transaction_time, '%Y-%m-%dT%H:%i:%s') BETWEEN ? AND ?", [$startDateTime, $endDateTime]);

        // Ambil data order berdasarkan filter
        $orders = $query->get();

        // Pastikan $summary didefinisikan sebelum dikirim ke View
        $summary = [
            'total_revenue' => $query->sum('payment_amount'),
            'total_discount' => $query->sum('discount_amount'),
            'total_tax' => $query->sum('tax'),
            'total_subtotal' => $query->sum('sub_total'),
            'total_service_charge' => $query->sum('service_charge'),
            'total' => $query->sum('sub_total') - $query->sum('discount_amount') - $query->sum('tax') + $query->sum('service_charge'),
        ];

        // Data untuk grafik ringkasan (JSON)
        $chartSummary = [
            'labels' => ['Total Revenue', 'Total Discount', 'Total Tax', 'Subtotal', 'Service Charge', 'Total'],
            'data' => [
                $summary['total_revenue'],
                $summary['total_discount'],
                $summary['total_tax'],
                $summary['total_subtotal'],
                $summary['total_service_charge'],
                $summary['total'],
            ],
        ];

        return view('pages.order_reports.index', compact('orders', 'summary', 'chartSummary', 'start_date', 'end_date'));
    }




    public function summary(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $query = Order::query();
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        $totalRevenue = $query->sum('payment_amount');
        $totalDiscount = $query->sum('discount_amount');
        $totalTax = $query->sum('tax');
        $totalServiceCharge = $query->sum('service_charge');
        $totalSubtotal = $query->sum('sub_total');
        $total = $totalSubtotal - $totalDiscount - $totalTax + $totalServiceCharge;
        return response()->json([
            'status' => 'success',
            'data' => [
                'total_revenue' => $totalRevenue,
                'total_discount' => $totalDiscount,
                'total_tax' => $totalTax,
                'total_subtotal' => $totalSubtotal,
                'total_service_charge' => $totalServiceCharge,
                'total' => $total,
            ]
        ], 200);
    }

    public function show($id)
    {
        $order = Order::with('orderItems.product')->findOrFail($id);

        return response()->json([
            'order' => $order,
            'items' => $order->orderItems->map(function ($item) {
                return [
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => number_format($item->price, 2),
                    'total' => number_format($item->quantity * $item->price, 2),
                ];
            })
        ]);
    }

}
