<?php

use App\Http\Controllers\CouponController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerOrderController;
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
use App\Http\Controllers\stockWarehouseController;
use App\Http\Controllers\FIFOHistoryController;
use App\Http\Controllers\GroceryShopController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\VehicleAssignmentController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\CustomerOrderReturnController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\RetailerOfferController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\MyProfileController;
use App\Http\Controllers\UserController;

Route::get('/', [AdminAuthController::class, 'loginForm'])->name('login.form');
Route::post('/admin-login', [AdminAuthController::class, 'login'])->name('admin.login');
Route::post('/admin-logout', [AdminAuthController::class, 'logout'])->name('logout');
Route::post('/reset-password', [AdminAuthController::class, 'resetPassword'])
    ->name('reset.password');

// Guest-only routes
// Route::middleware('guest')->group(function () {
//     Route::get('/', [AdminAuthController::class, 'loginForm'])->name('login.form');
//     Route::post('/admin-login', [AdminAuthController::class, 'login'])->name('admin.login');
// });

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/products', [ProductController::class, 'index'])
        ->middleware('permission:product.view');

    Route::get('/products/create', [ProductController::class, 'create'])
        ->middleware('permission:product.create');

    Route::post('/products', [ProductController::class, 'store'])
        ->middleware('permission:product.create');

    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])
        ->middleware('permission:product.edit');

    Route::delete('/products/{id}', [ProductController::class, 'destroy'])
        ->middleware('permission:product.delete');


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
    Route::resource('/sub-category', SubCategoryController::class);
    Route::resource('/units', UnitController::class);
    Route::resource('/product', ProductController::class);
    Route::resource('/warehouse', MasterWarehouseController::class);
    Route::resource('brands', BrandController::class);

    Route::get('get-categories-by-brand/{brand}', [ProductController::class, 'getCategoriesByBrand']);
    Route::get('get-sub-categories/{category}', [SubCategoryController::class, 'getSubCategories']);


    Route::get('/index-warehouse', [stockWarehouseController::class, 'indexWarehouse'])->name('index.addStock.warehouse');
    Route::get('/add-stock-warehouse', [stockWarehouseController::class, 'addStockForm'])->name('warehouse.addStockForm');
    Route::post('/add-stock-warehouse', [stockWarehouseController::class, 'addStock'])->name('warehouse.addStock');
    Route::get('/view-stock-warehouse/{id}', [stockWarehouseController::class, 'showStockForm'])->name('warehouse.viewStockForm');
    Route::get('/edit-stock-warehouse/{id}', [stockWarehouseController::class, 'editStockForm'])->name('warehouse.editStockForm');
    Route::put('/stock/{id}/update', [stockWarehouseController::class, 'updateStock'])
        ->name('stock.update');
    Route::delete('/stock/{id}/delete', [stockWarehouseController::class, 'destroyStock'])
        ->name('stock.delete');

    Route::get(
        '/get-sub-categories/{category}',
        [stockWarehouseController::class, 'byCategory']
    );


    Route::get('/get-categories-by-warehouse/{warehouse}', [stockWarehouseController::class, 'getCategories']);

    Route::get('/get-states/{country}', [LocationController::class, 'getStates']);
    Route::get('/get-districts/{state}', [LocationController::class, 'getDistricts']);
    Route::get('/get-talukas/{district}', [LocationController::class, 'getTalukas']);

    // double route 

    Route::get('/warehouse-transfers', [WarehouseTransferController::class, 'index'])->name('transfer.index');
    Route::get('/warehouse-transfer/create', [WarehouseTransferController::class, 'create'])
        ->name('transfer.create');

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


    Route::get(
        '/ajax/warehouse/{warehouse_id}/categories',
        [WarehouseTransferController::class, 'getCategoriesByWarehouse']
    )->name('ajax.warehouse.categories');

    Route::get(
        '/ajax/warehouse/{warehouse_id}/all-multiselect',
        [WarehouseTransferController::class, 'getWarehouseAllData']
    )->name('warehouse.multiselect');

    Route::get(
        '/ajax/warehouse-stock-data',
        [WarehouseTransferController::class, 'getWarehouseStockData']
    )->name('ajax.warehouse.stock.data');

    Route::get(
        '/warehouse-transfer/{batch}',
        [WarehouseTransferController::class, 'show']
    )->name('transfer.show');

    // double route end

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

    Route::put('/roles/update/{id}', [RoleController::class, 'update'])->name('roles.update');

    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])
        ->name('roles.destroy');


    //Route::get('/roles/{id}', [RoleController::class, 'edit'])
    //  ->name('roles.edit');

    Route::get('/roles-destroy/{id}', [RoleController::class, 'destroy'])
        ->name('roles.destroy');

    Route::resource('/vehicle-assignments', VehicleAssignmentController::class);

    Route::resource('/delivery-agents', DeliveryAgentController::class);

    Route::post(
        '/admin-assign-delivery',
        [DeliveryAgentController::class, 'assignDelivery']
    )->name('admin.assign.delivery');

    // Deliveries List
    Route::resource('/customer-orders', CustomerOrderController::class);
    Route::resource('/customer-returns', CustomerOrderReturnController::class);

    //////////////////////////////////////////////////////////////////////////////////////////////////

    // WAREHOUSE TRANSFER

    Route::prefix('warehouse-transfer')->name('transfer.')->group(function () {

        Route::get('/', [WarehouseTransferController::class, 'index'])->name('index');
        Route::get('/create', [WarehouseTransferController::class, 'create'])->name('create');
        Route::post('/store', [WarehouseTransferController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [WarehouseTransferController::class, 'edit'])->name('edit');
        Route::put('/{id}', [WarehouseTransferController::class, 'update'])->name('update');
        Route::delete('/{id}', [WarehouseTransferController::class, 'destroy'])->name('destroy');
    });

    Route::get(
        '/get-products-by-category/{category_id}',
        [WarehouseTransferController::class, 'getProductsByCategory']
    );
    Route::get(
        '/get-batches-by-product/{product_id}',
        [WarehouseTransferController::class, 'getBatchesByProduct']
    );
    Route::get('/get-warehouse-stock/{warehouse_id}/{batch_id}', [WarehouseTransferController::class, 'getWarehouseStock']);

    Route::get(
        '/ajax/warehouse/{warehouse_id}/categories',
        [WarehouseTransferController::class, 'getCategoriesByWarehouse']
    )->name('ajax.warehouse.categories');

    Route::get(
        '/warehouse-transfer/{batch}',
        [WarehouseTransferController::class, 'show']
    )->name('transfer.show');

    Route::get(
        '/ajax/product-batches',
        [WarehouseTransferController::class, 'getBatchesByProducts']
    )->name('ajax.product.batches');

    Route::get(
        '/get-batch-stock/{batch}',
        [WarehouseTransferController::class, 'getBatchStock']
    );


    // RETAILERS
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


    // RETAILER PRICING
    Route::prefix('retailer-pricing')->name('retailer-pricing.')->group(function () {

        Route::get('/', [RetailerPricingController::class, 'index'])->name('index');

        Route::get('/create', [RetailerPricingController::class, 'create'])->name('create');
        Route::post('/store', [RetailerPricingController::class, 'store'])->name('store');

        Route::get('/{pricing}/edit', [RetailerPricingController::class, 'edit'])->name('edit');
        Route::put('/{pricing}', [RetailerPricingController::class, 'update'])->name('update');

        Route::delete('/{pricing}', [RetailerPricingController::class, 'destroy'])->name('delete');

        Route::post('/bulk-upload', [RetailerPricingController::class, 'bulkUpload'])
            ->name('bulk.upload');

        Route::get(
            '/get-products-by-category/{category}',
            [RetailerPricingController::class, 'getProductsByCategory']
        );
    });


    // RETAILER ORDER
    Route::prefix('retailer-orders')->name('retailer-orders.')->group(function () {

        Route::get('/', [RetailerOrderController::class, 'index'])->name('index');

        Route::get('/create', [RetailerOrderController::class, 'create'])->name('create');

        Route::post('/store', [RetailerOrderController::class, 'store'])->name('store');

        // Auto price fetch
        Route::get(
            '/get-retailer-price/{retailer}/{product}',
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

        Route::get(
            '/ajax/get-warehouses-by-category/{retailer}/{category}',
            [RetailerOrderController::class, 'getWarehousesByCategory']
        )->name('ajax.get.warehouses');
    });

    //coupons
    Route::prefix('coupons')->name('coupons.')->group(function () {

        Route::get('/', [CouponController::class, 'index'])->name('index');
        Route::get('/create', [CouponController::class, 'create'])->name('create');
        Route::post('/', [CouponController::class, 'store'])->name('store');
        Route::get('/{coupon}/edit', [CouponController::class, 'edit'])->name('edit');
        Route::put('/{coupon}', [CouponController::class, 'update'])->name('update');
        Route::delete('/{coupon}', [CouponController::class, 'destroy'])->name('destroy');

        Route::get('/{coupon}', [CouponController::class, 'show'])->name('show');
    });
    // GROCER SHOP
    Route::prefix('grocery-shops')->name('grocery-shops.')->group(function () {

        Route::get('/', [GroceryShopController::class, 'index'])
            ->name('index');

        Route::get('/create', [GroceryShopController::class, 'create'])
            ->name('create');

        Route::post('/', [GroceryShopController::class, 'store'])
            ->name('store');

        Route::get('/{groceryShop}/edit', [GroceryShopController::class, 'edit'])
            ->name('edit');

        Route::put('/{groceryShop}', [GroceryShopController::class, 'update'])
            ->name('update');

        Route::delete('/{groceryShop}', [GroceryShopController::class, 'destroy'])
            ->name('destroy');

        Route::get('/{groceryShop}', [GroceryShopController::class, 'show'])
            ->name('show');
    });

    Route::get('/talukas/by-district/{district}', [GroceryShopController::class, 'byDistrict'])
        ->name('talukas.by-district');


    //  PURCHASE ORDER  
    Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create']);
    Route::post('/purchase-orders/store', [PurchaseOrderController::class, 'store']);

    // PURCHASE ORDER AJAX
    Route::get(
        '/po/subcategories/{category_id}',
        [PurchaseOrderController::class, 'getSubCategories']
    );

    Route::get(
        '/po/products/{sub_category_id}',
        [PurchaseOrderController::class, 'getProducts']
    );

    Route::get(
        '/po/all-products',
        [PurchaseOrderController::class, 'getAllProducts']
    );

    Route::get('/po/product-available-qty/{product}', [PurchaseOrderController::class, 'getAvailableQty']);

    Route::get(
        '/purchase-orders/{po}/invoice',
        [PurchaseOrderController::class, 'invoice']
    )
        ->name('purchase.invoice');

    Route::get(
        '/purchase-orders',
        [PurchaseOrderController::class, 'index']
    )
        ->name('purchase.orders.index');



    // PRODUCT BATCH MANAGEMENT
    Route::get('/batches', [ProductBatchController::class, 'index'])->name('batches.index');
    Route::get('/batches/create', [ProductBatchController::class, 'create'])->name('batches.create');
    Route::post('/batches', [ProductBatchController::class, 'store'])->name('batches.store');
    Route::get('/get-products/{category_id}', [ProductBatchController::class, 'getProductsByCategory']);

    // Edit form
    Route::get('/batches/{id}/edit', [ProductBatchController::class, 'edit'])->name('batches.edit');
    // Update
    Route::put('/batches/{id}', [ProductBatchController::class, 'update'])->name('batches.update');

    // Soft delete
    Route::delete('/batches/{id}', [ProductBatchController::class, 'destroy'])->name('batches.destroy');

    Route::get('/batches/{batch}', [ProductBatchController::class, 'show'])
        ->name('batches.show');

    Route::get('/ws/categories/{warehouse}', [ProductBatchController::class, 'getCategoriesByWarehouse']);
    Route::get('/ws/subcategories/{warehouse}/{category}', [ProductBatchController::class, 'getSubCategories']);
    Route::get('/ws/products-by-sub/{warehouse}/{sub}', [ProductBatchController::class, 'getProductsBySubCategory']);
    Route::get('/ws/quantity/{warehouse}/{product}', [ProductBatchController::class, 'getProductQuantity']);


    // EXPIRY ALERT
    Route::get('/expiry-alerts', [ProductBatchController::class, 'expiryAlerts'])->name('batches.expiry');

    // SELL PRODUCT
    Route::get('/sell', [FIFOHistoryController::class, 'index'])->name('sell.index');
    Route::get('/sale/{product?}', [StockController::class, 'create'])
        ->name('sale.create');
    Route::post('/sale', [StockController::class, 'store'])->name('sale.store');

    Route::get('/sell/ws/categories/{warehouse}', [StockController::class, 'getCategoriesByWarehouse']);
    Route::get('/sell/ws/subcategories/{warehouse}/{category}', [StockController::class, 'getSubCategoriesByWarehouse']);
    Route::get('/sell/ws/products/{warehouse}/{subCategory}', [StockController::class, 'getProductsBySubCategory']);
    Route::get('/sell/ws/quantity/{warehouse}/{product}', [StockController::class, 'getProductQuantity']);

    // AJAX route to get products by category
    Route::get('/get-products-by-category/{categoryId}', [StockController::class, 'getProductsByCategory']);

    Route::get('/get-stock/{warehouse}/{product}', function ($warehouseId, $productId) {
        $stock = \App\Models\WarehouseStock::where('warehouse_id', $warehouseId)
            ->where('product_id', $productId)
            ->sum('quantity');
        return response()->json(['stock' => $stock]);
    });

    // Approval
    Route::get(
        '/warehouse-transfers/approval',
        [ApprovalController::class, 'index']
    )->name('warehouse.transfer.index');
    Route::post(
        '/warehouse-transfers/{transfer}/approve',
        [ApprovalController::class, 'approve']
    )->name('warehouse.transfer.approve');
    Route::post(
        '/warehouse-transfer/{transfer}/reject',
        [ApprovalController::class, 'reject']
    )->name('warehouse.transfer.reject');


    //////////////////////////////////////////////////////////////////////////////////////////////////////

    Route::prefix('supplier')->name('supplier.')->group(function () {

        Route::get('/', [SupplierController::class, 'index'])->name('index');

        // // CREATE
        Route::get('/create', [SupplierController::class, 'create'])->name('create');
        Route::post('/store', [SupplierController::class, 'store'])->name('store');

        // // EDIT / UPDATE
        Route::get('/{id}/edit', [SupplierController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SupplierController::class, 'update'])->name('update');
        Route::get('/{id}', [SupplierController::class, 'show'])
            ->name('show');
        // // DELETE
        Route::delete('/{id}', [SupplierController::class, 'destroy'])
            ->name('destroy');
        Route::get('/get-districts/{stateId}', [SupplierController::class, 'getDistricts']);
        Route::get('/get-talukas/{districtId}', [SupplierController::class, 'getTalukas']);
    });

    //product offer 

    Route::prefix('offer')->group(function () {
        Route::resource('offers', OfferController::class);
        Route::get('products-by-category/{category}', [OfferController::class, 'productsByCategory']);
    });
    Route::prefix('retailer-offers')->name('retailer-offers.')->group(function () {
        Route::get('/', [RetailerOfferController::class, 'index'])->name('index');
        Route::get('create', [RetailerOfferController::class, 'create'])->name('create');
        Route::post('store', [RetailerOfferController::class, 'store'])->name('store');
        Route::get('{id}', [RetailerOfferController::class, 'show'])->name('show');
        Route::get('{id}/edit', [RetailerOfferController::class, 'edit'])->name('edit');
        Route::put('{id}', [RetailerOfferController::class, 'update'])->name('update');
        Route::delete('{id}', [RetailerOfferController::class, 'destroy'])->name('destroy');
    });

    // Route::get('warehouse-stock/report', [ReportsController::class, 'warehouse_stock_report'])
    //     ->name('warehouse-stock.report');
    Route::match(['get', 'post'], 'warehouse-stock/report', [ReportsController::class, 'warehouse_stock_report'])
        ->name('warehouse-stock.report');


    Route::get('/dev/run/{action}', function ($action) {
        try {
            switch ($action) {
                case 'clear':
                    Artisan::call('config:clear');
                    Artisan::call('cache:clear');
                    Artisan::call('route:clear');
                    Artisan::call('view:clear');
                    return "Cleared config, cache, route, and view.";

                case 'migrate':
                    Artisan::call('session:table');
                    Artisan::call('migrate');
                    return "Migration completed successfully!";

                case 'migrate-fresh':
                    Artisan::call('migrate:fresh', ['--seed' => true]);
                    return "Fresh migration and seed completed!";

                case 'seed':
                    Artisan::call('db:seed');
                    return "Database seeding completed!";

                case 'seed-menu':
                    Artisan::call('db:seed', ['--class' => 'MenuSeeder']);
                    return "MenuSeeder database seeding completed!";

                case 'seed-role':
                    Artisan::call('db:seed', ['--class' => 'RoleSeeder']);
                    return "RoleSeeder database seeding completed!";

                case 'storage-link':
                    Artisan::call('storage:link');
                    $output = Artisan::output();
                    return "Storage link created!"  . nl2br($output);

                case 'install':
                    exec('composer install');
                    return "composer install executed!";
                default:
                    return "Invalid action: $action";
            }
        } catch (\Exception $e) {
            return "Error running action [$action]: " . $e->getMessage();
        }
    });


    //auth closing
});
Route::get('/user-profile', function () {
    return view('profile');
})->name('user-profile');

Route::put('/profile/update', [UserController::class, 'updateProfile'])
    ->name('profile.update');
Route::get('/users/profile', [UserController::class, 'profile'])
    ->name('user.profile');
