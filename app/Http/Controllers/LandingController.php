<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        // Ambil range dari query string, default 30 hari
        $range = $request->get('range', 30);

        // Validasi range (anti iseng)
        if (!in_array($range, [7, 30, 90])) {
            $range = 30;
        }

        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_qty'),
                DB::raw('MAX(order_items.price) as unit_price')
            )
            ->where('order_items.created_at', '>=', Carbon::now()->subDays($range))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        return view('landing', compact('topProducts', 'range'));
    }
}
