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
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\WarehouseTransferController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RetailerController;
use App\Http\Controllers\RetailerPricingController;
use App\Http\Controllers\RetailerOrderController;




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
Route::get('/user-profile', [AdminAuthController::class, 'index'])->name('user.profile');
Route::get('/user-profile/create', [AdminAuthController::class, 'createUser'])->name('user.create');
Route::post('/user-profile/store', [AdminAuthController::class, 'store'])->name('user.store');
Route::get('/user/{id}', [AdminAuthController::class, 'show'])->name('user.show');
Route::get('/user/{id}/edit', [AdminAuthController::class, 'editUser'])->name('user.edit');
Route::put('/user/{id}', [AdminAuthController::class, 'update'])->name('user.update');
Route::delete('/user/{id}', [AdminAuthController::class, 'destroy'])->name('user.destroy');

Route::get('rolepermission', [RolePermissionController::class, 'RolePermission'])->name('RolePermission');
Route::post('rolepermission/store', [RolePermissionController::class, 'Store'])->name('Store');
Route::get('/get-role-permissions/{id}', [RolePermissionController::class, 'getRolePermissions']);

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
    '/get-categories-by-warehouse/{warehouse_id}',
    [WarehouseTransferController::class, 'getCategoriesByWarehouse']
)->name('warehouse.categories');

    

Route::get('/roles/index', [RoleController::class, 'index'])
    ->name('roles.index');

Route::get('/roles/create', [RoleController::class, 'create'])
    ->name('roles.create');

Route::post('/roles/store', [RoleController::class, 'store'])
    ->name('roles.store');

Route::get('/roles-show/{id}', [RoleController::class, 'show'])
    ->name('roles.show');

Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])
->name('roles.edit');

Route::put('/roles/update/{id}',[RoleController::class, 'update'])->name('roles.update');

Route::delete('/roles/{id}', [RoleController::class, 'destroy'])
    ->name('roles.destroy');

    
//Route::get('/roles/{id}', [RoleController::class, 'edit'])
  //  ->name('roles.edit');
    
Route::get('/roles-destroy/{id}', [RoleController::class, 'destroy'])
    ->name('roles.destroy');

Route::resource('/delivery-agents', DeliveryAgentController::class);

// Deliveries List
Route::get('/deliveries', [DeliveryAgentController::class, 'index'])
    ->name('deliveries.index');
    
Route::prefix('retailers')->name('retailers.')->group(function () {

    Route::get('/', [RetailerController::class, 'index'])->name('index');

    // CREATE
    Route::get('/create', [RetailerController::class, 'create'])->name('create');
    Route::post('/store', [RetailerController::class, 'store'])->name('store');

    // EDIT / UPDATE
    Route::get('/{retailer}/edit', [RetailerController::class, 'edit'])->name('edit');
    Route::put('/{retailer}', [RetailerController::class, 'update'])->name('update');

    // DELETE
    Route::delete('/{retailer}', [RetailerController::class, 'delete']);

    // ACTIVATE / DEACTIVATE
    Route::patch('/{retailer}/toggle-status', [RetailerController::class, 'toggleStatus'])
        ->name('toggle.status');
});


Route::prefix('retailer-pricing')->name('retailer-pricing.')->group(function () {

    Route::get('/', [RetailerPricingController::class, 'index'])->name('index');

    Route::get('/create', [RetailerPricingController::class, 'create'])->name('create');
    Route::post('/store', [RetailerPricingController::class, 'store'])->name('store');

    Route::get('/{pricing}/edit', [RetailerPricingController::class, 'edit'])->name('edit');
    Route::put('/{pricing}', [RetailerPricingController::class, 'update'])->name('update');

    Route::delete('/{pricing}', [RetailerPricingController::class, 'destroy'])->name('delete');

    Route::post('/bulk-upload', [RetailerPricingController::class, 'bulkUpload'])
        ->name('bulk.upload');

    Route::get('/get-products-by-category/{category}',
        [RetailerPricingController::class, 'getProductsByCategory']);

});

Route::prefix('retailer-orders')->name('retailer-orders.')->group(function () {

    Route::get('/', [RetailerOrderController::class, 'index'])->name('index');

    Route::get('/create', [RetailerOrderController::class, 'create'])->name('create');

    Route::post('/store', [RetailerOrderController::class, 'store'])->name('store');

    // 🔥 Auto price fetch
    Route::get('/get-retailer-price/{retailer}/{product}',
        [RetailerOrderController::class, 'getRetailerPrice']
    )->name('get.price');

    Route::get(
        '/get-categories-by-retailer/{retailer}',
        [RetailerOrderController::class, 'getCategoriesByRetailer']
    )->name('get.categories');

    Route::get(
        '/get-products-by-retailer/{retailer}/{category}',
        [RetailerOrderController::class, 'getProductsByRetailerCategory']
    )->name('get.products');

});


