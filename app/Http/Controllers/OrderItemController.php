<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderItemController extends Controller
{
    // public function index(Request $request)
    // {
    //     // Ambil input tanggal, default ke bulan ini jika tidak diisi
    //     $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
    //     $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

    //     // Cek apakah start date lebih besar dari end date
    //     if ($startDate > $endDate) {
    //         return redirect()->back()->with('error', 'Tanggal mulai tidak bisa lebih besar dari tanggal akhir.');
    //     }

    //     // Konversi ke format waktu penuh (00:00:00 hingga 23:59:59)
    //     $startDateTime = Carbon::parse($startDate)->startOfDay();
    //     $endDateTime = Carbon::parse($endDate)->endOfDay();

    //     // Query untuk mendapatkan makanan terlaris berdasarkan rentang tanggal
    //     $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
    //         ->whereHas('order', function ($query) use ($startDateTime, $endDateTime) {
    //             $query->whereBetween('created_at', [$startDateTime, $endDateTime]);
    //         })
    //         ->groupBy('product_id')
    //         ->orderByDesc('total_quantity')
    //         ->with('product') // Ambil data produk
    //         ->paginate(10);

    //     return view('pages.order_item_reports.index', compact('topProducts', 'startDate', 'endDate'));
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

        // Konversi ke format waktu penuh
        $startDateTime = Carbon::parse($start_date)->startOfDay();
        $endDateTime = Carbon::parse($end_date)->endOfDay();

        // Query untuk mendapatkan produk terlaris
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->whereHas('order', function ($query) use ($startDateTime, $endDateTime) {
                $query->whereBetween('created_at', [$startDateTime, $endDateTime]);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product') // Ambil data produk
            ->paginate(10);

        // Data untuk grafik (JSON)
        $chartData = [
            'labels' => $topProducts->pluck('product.name'),
            'data' => $topProducts->pluck('total_quantity'),
        ];

        return view('pages.order_item_reports.index', compact('topProducts', 'start_date', 'end_date', 'chartData'));
    }
}
