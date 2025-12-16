<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterWarehouseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductBatchController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\WarehouseTransferController;

Route::get('/', [AdminAuthController::class, 'loginForm'])->name('login.form');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('/category', CategoryController::class);
Route::resource('/product', ProductController::class);
Route::resource('/warehouse', MasterWarehouseController::class);


Route::get('/batches', [ProductBatchController::class, 'index'])->name('batches.index');
Route::get('/batches/create', [ProductBatchController::class, 'create']);
Route::post('/batches', [ProductBatchController::class, 'store']);
Route::get('/get-products/{category_id}', 
    [ProductBatchController::class, 'getProductsByCategory']
);


Route::get('/sale/{product?}', [StockController::class, 'create'])
    ->name('sale.create');
    
// AJAX route to get products by category
Route::get('/get-products-by-category/{categoryId}', [StockController::class, 'getProductsByCategory']);

Route::post('/sale', [StockController::class, 'store']);

Route::get('/expiry-alerts', [ProductBatchController::class, 'expiryAlerts']);



Route::get('/warehouse-transfers', [WarehouseTransferController::class, 'index'])->name('transfer.index');

Route::get('/warehouse-transfer', [WarehouseTransferController::class, 'create']);
Route::post('/warehouse-transfer', [WarehouseTransferController::class, 'store']);
Route::get('/get-products-by-category/{category_id}', 
    [WarehouseTransferController::class, 'getProductsByCategory']
);

Route::get('/get-batches-by-product/{product_id}', 
    [WarehouseTransferController::class, 'getBatchesByProduct']
);
Route::get('/get-warehouse-stock/{warehouse_id}/{batch_id}', [WarehouseTransferController::class, 'getWarehouseStock']);



