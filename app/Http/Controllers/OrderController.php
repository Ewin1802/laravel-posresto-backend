<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;

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

    public function index(Request $request)
    {
        // Ambil input tanggal dari request
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        // Mulai query dengan model Order
        $query = Order::query();

        // Jika tanggal difilter, gunakan filter `whereRaw`
        if ($start_date && $end_date) {
            $query->whereRaw("STR_TO_DATE(transaction_time, '%Y-%m-%dT%H:%i:%s') BETWEEN ? AND ?", [
                $start_date . ' 00:00:00',
                $end_date . ' 23:59:59',
            ]);

            // Ambil seluruh data tanpa paginasi
            $orders = $query->get();
        } else {
            // Jika tidak ada filter tanggal, tampilkan daftar kosong
            $orders = collect(); // Koleksi kosong
        }

        // Perhitungan ringkasan data berdasarkan query
        $summary = [
            'total_revenue' => $query->sum('payment_amount'),
            'total_discount' => $query->sum('discount_amount'),
            'total_tax' => $query->sum('tax'),
            'total_subtotal' => $query->sum('sub_total'),
            'total_service_charge' => $query->sum('service_charge'),
            'total' => $query->sum('sub_total') - $query->sum('discount_amount') - $query->sum('tax') + $query->sum('service_charge'),
        ];

        // Tampilkan halaman dengan data awal
        return view('pages.order_reports.index', compact('orders', 'summary', 'start_date', 'end_date'));
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
