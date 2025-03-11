<?php

use Illuminate\Support\Facades\Route;
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
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // return view('pages.auth.login');
    return view('landing');
});
Route::get('/login', function () {
    return view('pages.auth.login');
})->name('login');


Route::middleware(['auth'])->group(function () {
    Route::get('home', function () {
        return view('pages.dashboard');
    })->name('home');

    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('products', ProductController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('discounts', DiscountController::class);

        Route::resource('order', OrderController::class);
        Route::get('/order-reports', [OrderController::class, 'index'])->name('order_reports.index');
        Route::post('products/update/{id}', [ProductController::class, 'update'])->name('products.newupdate');
        Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/top-products', [OrderItemController::class, 'index'])->name('top.products');

        Route::resource('inventories', InventoryController::class);
        Route::resource('inventory_out', InventoryOutController::class);
        Route::resource('bahans', BahanController::class);
        Route::get('/inventory-reports', [InventoryReportController::class, 'index'])->name('inventory.reports');
        Route::get('/laporan-keuangan', [FinancialReportController::class, 'index'])->name('financial.report');

    });
});
