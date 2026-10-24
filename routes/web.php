<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryOutController;
use App\Http\Controllers\InventoryReportController;
use App\Http\Controllers\BahanController;
use App\Http\Controllers\FinancialReportController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (NO AUTH)
|--------------------------------------------------------------------------
*/

Route::get('/', [LandingController::class, 'index'])->name('landing');

/*
| Halaman login ditangani Fortify (Fortify::loginView). Route GET /login
| di bawah ini dipertahankan agar URL lama tetap jalan.
*/
Route::get('/login', function () {
    return auth()->check() ? redirect()->route('home') : view('pages.auth.login');
})->name('login');

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/home', [DashboardController::class, 'index'])->name('home');

    Route::middleware(['role:admin'])->group(function () {

        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('products', ProductController::class)->except(['show']);
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('discounts', DiscountController::class)->except(['show']);

        Route::resource('order', OrderController::class)->only(['index']);
        Route::get('/order-reports', [OrderController::class, 'index'])->name('order_reports.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->whereNumber('id')->name('orders.show');

        Route::get('/top-products', [OrderItemController::class, 'index'])->name('top.products');

        Route::resource('inventories', InventoryController::class)->except(['show', 'destroy']);
        Route::resource('inventory_out', InventoryOutController::class)->only(['index', 'create', 'store']);
        Route::resource('bahans', BahanController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);

        Route::get('/inventory-reports', [InventoryReportController::class, 'index'])->name('inventory.reports');
        Route::get('/laporan-keuangan', [FinancialReportController::class, 'index'])->name('financial.report');
    });
});
