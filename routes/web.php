<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryAgentController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MasterWarehouseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductBatchController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\WarehouseTransferController;


Route::get('/', [AdminAuthController::class, 'loginForm'])->name('login.form');
Route::post('/admin-login', [AdminAuthController::class, 'login'])->name('admin.login');
Route::post('/admin-logout', [AdminAuthController::class, 'logout'])->name('logout');
Route::post('/reset-password', [AdminAuthController::class, 'resetPassword'])
    ->name('reset.password');
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/forgot-password', function () {
        return view('admin-login.password.reset');
    })->name('forgot.password');
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
Route::resource('brands', BrandController::class);

Route::get('/index-warehouse', [MasterWarehouseController::class, 'indexWarehouse'])->name('index.addStock.warehouse');
Route::get('/add-stock-warehouse', [MasterWarehouseController::class, 'addStockForm'])->name('warehouse.addStockForm');
Route::post('/add-stock-warehouse', [MasterWarehouseController::class, 'addStock'])->name('warehouse.addStock');
Route::get('/view-stock-warehouse/{id}', [MasterWarehouseController::class, 'showStockForm'])->name('warehouse.viewStockForm');
Route::get('/edit-stock-warehouse/{id}', [MasterWarehouseController::class, 'editStockForm'])->name('warehouse.editStockForm');
Route::put('/stock/{id}/update', [MasterWarehouseController::class, 'updateStock'])
    ->name('stock.update');
Route::delete('/stock/{id}/delete', [MasterWarehouseController::class, 'destroyStock'])
    ->name('stock.delete');

Route::get('/get-categories-by-warehouse/{warehouse}', [MasterWarehouseController::class, 'getCategories']);



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

Route::get('/batches/{batch}', [ProductBatchController::class, 'show'])
    ->name('batches.show');



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


//Route::get('/expiry-alerts', [ProductBatchController::class, 'expiryAlerts']);
Route::get(
    '/expiry-alerts',
    [ProductBatchController::class, 'expiryAlerts']
)->name('batches.expiry');


Route::get('/warehouse-transfers', [WarehouseTransferController::class, 'index'])->name('transfer.index');

Route::get('/warehouse-transfer', [WarehouseTransferController::class, 'create'])->name('transfer.create');
Route::post('/warehouse-transfer', [WarehouseTransferController::class, 'store'])->name('transfer.store');
Route::get('/warehouse-transfer/{id}/edit', [WarehouseTransferController::class, 'edit'])->name('transfer.edit');
Route::put('/warehouse-transfer/{id}', [WarehouseTransferController::class, 'update'])->name('transfer.update');
// Soft delete
Route::delete('/warehouse-transfer/{id}', [WarehouseTransferController::class, 'destroy'])->name('transfer.destroy');

Route::get(
    '/get-products-by-category/{category_id}',
    [WarehouseTransferController::class, 'getProductsByCategory']
);
Route::get(
    '/get-batches-by-product/{product_id}',
    [WarehouseTransferController::class, 'getBatchesByProduct']
);
Route::get('/get-warehouse-stock/{warehouse_id}/{batch_id}', [WarehouseTransferController::class, 'getWarehouseStock']);

Route::get('/warehouse-transfer/{batch}', [WarehouseTransferController::class, 'show'])
    ->name('transfer.show');

Route::get(
    '/check-batch-validity/{batch_id}',
    [WarehouseTransferController::class, 'checkBatchValidity']
);

Route::get(
    '/get-categories-by-warehouse/{warehouse_id}',
    [WarehouseTransferController::class, 'getCategoriesByWarehouse']
);


Route::resource('/delivery-agents', DeliveryAgentController::class);

// Deliveries List
Route::get('/deliveries', [DeliveryAgentController::class, 'index'])
    ->name('deliveries.index');
