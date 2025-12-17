<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MasterWarehouseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductBatchController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\WarehouseTransferController;

Route::get('/', [AdminAuthController::class, 'loginForm'])->name('login.form');
Route::post('/admin-login', [AdminAuthController::class, 'login'])->name('admin.login');
Route::post('/admin-logout', [AdminAuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/forgot-password', function () {
        return view('admin-login.password.reset');
    })->name('forgot.password');
    Route::post('/reset-password', [AdminAuthController::class, 'resetPassword'])
        ->name('reset.password');
});

// User Profile 
Route::get('/user-profile', [AdminAuthController::class, 'index'])
    ->name('user.profile');
Route::get('/user-profile/create', [AdminAuthController::class, 'createUser'])
    ->name('user.create');
Route::post('/user-profile/store', [AdminAuthController::class, 'store'])
    ->name('user.store');
Route::get('/user/{id}', [AdminAuthController::class, 'show'])
    ->name('user.show');
Route::get('/user/{id}/edit', [AdminAuthController::class, 'editUser'])->name('user.edit');
Route::put('/user/{id}', [AdminAuthController::class, 'update'])->name('user.update');
Route::delete('/user/{id}', [AdminAuthController::class, 'destroy'])
    ->name('user.destroy');

Route::resource('/category', CategoryController::class);
Route::resource('/product', ProductController::class);
Route::resource('/warehouse', MasterWarehouseController::class);

Route::get('/get-states/{country}', [LocationController::class, 'getStates']);
Route::get('/get-districts/{state}', [LocationController::class, 'getDistricts']);
Route::get('/get-talukas/{district}', [LocationController::class, 'getTalukas']);

Route::get('/batches', [ProductBatchController::class, 'index'])->name('batches.index');
Route::get('/batches/create', [ProductBatchController::class, 'create'])->name('batches.create');
Route::post('/batches', [ProductBatchController::class, 'store'])->name('batches.store');
Route::get(
    '/get-products/{category_id}',
    [ProductBatchController::class, 'getProductsByCategory']
);

// Edit form
Route::get('/batches/{id}/edit', [ProductBatchController::class, 'edit'])->name('batches.edit');

// Update
Route::put('/batches/{id}', [ProductBatchController::class, 'update'])->name('batches.update');

// Soft delete
Route::delete('/batches/{id}', [ProductBatchController::class, 'destroy'])->name('batches.destroy');



Route::get('/sale/{product?}', [StockController::class, 'create'])
    ->name('sale.create');
Route::post('/sale', [StockController::class, 'store'])->name('sale.store');

// AJAX route to get products by category
Route::get('/get-products-by-category/{categoryId}', [StockController::class, 'getProductsByCategory']);

Route::get('/get-stock/{warehouse}/{product}', function ($warehouseId, $productId) {
    $stock = \App\Models\WarehouseStock::where('warehouse_id', $warehouseId)
        ->where('product_id', $productId)
        ->sum('quantity');
    return response()->json(['stock' => $stock]);
});


Route::get('/expiry-alerts', [ProductBatchController::class, 'expiryAlerts']);


Route::get('/warehouse-transfers', [WarehouseTransferController::class, 'index'])->name('transfer.index');

Route::get('/warehouse-transfer', [WarehouseTransferController::class, 'create']);
Route::post('/warehouse-transfer', [WarehouseTransferController::class, 'store']);

Route::get(
    '/get-products-by-category/{category_id}',
    [WarehouseTransferController::class, 'getProductsByCategory']
);
Route::get(
    '/get-batches-by-product/{product_id}',
    [WarehouseTransferController::class, 'getBatchesByProduct']
);
Route::get('/get-warehouse-stock/{warehouse_id}/{batch_id}', [WarehouseTransferController::class, 'getWarehouseStock']);
