<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{


    public function saveOrder(Request $request)
    {
        // Validasi request
        $request->validate([
            'payment_amount' => 'required',
            'sub_total' => 'required',
            'tax' => 'required',
            'discount' => 'required',
            'discount_amount' => 'required',
            'service_charge' => 'required',
            'total' => 'required',
            'payment_method' => 'required',
            'total_item' => 'required',
            'id_kasir' => 'required',
            'nama_kasir' => 'required',
            'transaction_time' => 'required',
            'customer_name' => 'nullable|string',
            'order_items' => 'required|array',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                // Kunci sementara row yang sedang dicek
                $existingOrder = DB::table('orders')
                    ->where('transaction_time', $request->transaction_time)
                    ->when($request->filled('customer_name'), function ($query) use ($request) {
                        return $query->where('customer_name', $request->customer_name);
                    })
                    ->lockForUpdate() // Lock baris hasil query untuk mencegah race
                    ->first();

                if ($existingOrder) {
                    return response()->json([
                        'status' => 'exists',
                        'message' => 'Order already exists, ignoring duplicate entry.',
                        'data' => $existingOrder
                    ], 200);
                }

                // Buat order baru
                $order = Order::create([
                    'payment_amount' => $request->payment_amount,
                    'sub_total' => $request->sub_total,
                    'tax' => $request->tax,
                    'discount' => $request->discount,
                    'discount_amount' => $request->discount_amount,
                    'service_charge' => $request->service_charge,
                    'total' => $request->total,
                    'payment_method' => $request->payment_method,
                    'total_item' => $request->total_item,
                    'id_kasir' => $request->id_kasir,
                    'nama_kasir' => $request->nama_kasir,
                    'transaction_time' => $request->transaction_time,
                    'customer_name' => $request->customer_name ?? null,
                ]);

                foreach ($request->order_items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['id_product'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price']
                    ]);
                }

                return response()->json([
                    'status' => 'success',
                    'data' => $order
                ], 200);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan order',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // public function index(Request $request)
    // {
    //     $start_date = $request->input('start_date');
    //     $end_date = $request->input('end_date');
    //     if ($start_date && $end_date) {
    //         $orders = Order::whereBetween('created_at', [$start_date, $end_date])->get();
    //     } else {
    //         $orders = Order::all();
    //     }
    //     return response()->json([
    //         'status' => 'success',
    //         'data' => $orders
    //     ], 200);
    // }
    public function index(Request $request)
    {
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        if ($start_date && $end_date) {

            $start = substr($start_date, 0, 10);
            $end = substr($end_date, 0, 10);

            $orders = Order::whereRaw("
            DATE(SUBSTRING_INDEX(transaction_time, 'T', 1)) BETWEEN ? AND ?
        ", [$start, $end])->get();
        } else {
            $orders = Order::all();
        }

        return response()->json([
            'status' => 'success',
            'data' => $orders
        ], 200);
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
}
