<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LandingController extends Controller
{
    public function index(Request $request)
{
    $range = $request->get('range', 30);
    if (!in_array($range, [7, 30, 90])) {
        $range = 30;
    }

    // 🔹 TOP PRODUCTS (untuk tabel + badge)
    $topProducts = DB::table('order_items')
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->select(
            'products.id',
            'products.name',
            DB::raw('SUM(order_items.quantity) as total_qty'),
            DB::raw('MAX(order_items.price) as unit_price')
        )
        ->havingRaw('SUM(order_items.quantity) > 0')

        ->where('order_items.created_at', '>=', Carbon::now()->subDays($range))
        ->groupBy('products.id', 'products.name')
        ->orderByDesc('total_qty')
        ->limit(10)
        ->get();

    // 🔹 AMBIL ID TOP 1–3 (kalau ada)
    $topProductIds = $topProducts->isNotEmpty()
    ? $topProducts->take(3)->pluck('id')->toArray()
    : [];


    // 🔹 SLIDER: SELALU DARI PRODUCTS
    $sliderProducts = DB::table('products')
        ->where('status', 1)
        ->whereNotNull('image')
        ->orderBy('is_favorite', 'desc')
        ->limit(5)
        ->get();

    return view('landing', compact(
        'topProducts',
        'sliderProducts',
        'topProductIds',
        'range'
    ));
}

}
