<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//login api
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);

//logout api
//harus pakai midleware karena logout dipanggil harus dalam posisi login
Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware('auth:sanctum');

//products api
Route::get('/products', [App\Http\Controllers\Api\ProductController::class, 'index'])->middleware('auth:sanctum');
Route::post('/products', [App\Http\Controllers\Api\ProductController::class, 'store'])->middleware('auth:sanctum');
Route::post('/products/edit', [App\Http\Controllers\Api\ProductController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/products/{id}', [App\Http\Controllers\Api\ProductController::class, 'destroy'])->middleware('auth:sanctum');
//categories api
Route::apiResource('/api-categories', App\Http\Controllers\Api\CategoryController::class)->middleware('auth:sanctum');

//orders api
Route::post('/save-order', [App\Http\Controllers\Api\OrderController::class, 'saveOrder'])->middleware('auth:sanctum');

//discounts api
Route::get('/api-discounts', [App\Http\Controllers\Api\DiscountController::class, 'index'])->middleware('auth:sanctum');

Route::post('/api-discounts', [App\Http\Controllers\Api\DiscountController::class, 'store'])->middleware('auth:sanctum');

// api resource report

Route::get('/orders/{date?}', [App\Http\Controllers\Api\OrderController::class, 'index'])->middleware('auth:sanctum');
Route::get('/summary/{date?}', [App\Http\Controllers\Api\OrderController::class, 'summary'])->middleware('auth:sanctum');
Route::get('/order-item/{date?}', [App\Http\Controllers\Api\OrderItemController::class, 'index'])->middleware('auth:sanctum');
Route::get('/order-sales', [App\Http\Controllers\Api\OrderItemController::class, 'orderSales'])->middleware('auth:sanctum');


// // Middleware untuk autentikasi dan throttle
// $authThrottle = ['auth:sanctum', 'throttle:500,1']; // Maks 100 request per menit
// $generalThrottle = ['throttle:200,1']; // Maks 60 request per menit untuk API terbuka

// // Mendapatkan data user yang login
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// // Login API (Tanpa throttle agar tidak membatasi login)
// Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);

// // Logout API (Harus dalam keadaan login)
// Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware($authThrottle);

// // Products API
// Route::middleware($authThrottle)->group(function () {
//     Route::get('/products', [App\Http\Controllers\Api\ProductController::class, 'index']);
//     Route::post('/products', [App\Http\Controllers\Api\ProductController::class, 'store']);
//     Route::post('/products/edit', [App\Http\Controllers\Api\ProductController::class, 'update']);
//     Route::delete('/products/{id}', [App\Http\Controllers\Api\ProductController::class, 'destroy']);
// });

// // Categories API
// Route::apiResource('/api-categories', App\Http\Controllers\Api\CategoryController::class)->middleware($authThrottle);

// // Orders API (Batasi order untuk mencegah spam)
// Route::post('/save-order', [App\Http\Controllers\Api\OrderController::class, 'saveOrder'])->middleware('auth:sanctum'); // Maks 50 request per menit

// // Discounts API
// Route::middleware($authThrottle)->group(function () {
//     Route::get('/api-discounts', [App\Http\Controllers\Api\DiscountController::class, 'index']);
//     Route::post('/api-discounts', [App\Http\Controllers\Api\DiscountController::class, 'store']);
// });

// // Report API (Cegah spam request laporan)
// Route::middleware('throttle:30,1')->group(function () { // Maks 30 request per menit
//     Route::get('/orders/{date?}', [App\Http\Controllers\Api\OrderController::class, 'index']);
//     Route::get('/summary/{date?}', [App\Http\Controllers\Api\OrderController::class, 'summary']);
//     Route::get('/order-item/{date?}', [App\Http\Controllers\Api\OrderItemController::class, 'index']);
//     Route::get('/order-sales', [App\Http\Controllers\Api\OrderItemController::class, 'orderSales']);
// });
