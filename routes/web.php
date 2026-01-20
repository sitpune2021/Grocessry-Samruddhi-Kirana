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
use App\Http\Controllers\DistrictToTalukaTransferController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\RetailerOfferController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\MyProfileController;
use App\Http\Controllers\TalukaTransferController;
use App\Http\Controllers\RefundExchangeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LowStockController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\WarehouseStockReturnController;
use App\Http\Controllers\WarehouseTransferRequestController;
use App\Http\Controllers\TransferChallanController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\DistrictToDistrictTransferController;

// Website Route
use App\Http\Controllers\BannerController;
use App\Http\Controllers\DistrictToDistrictApprovalController;
use App\Http\Controllers\DistrictToTalukaApprovalController;
use App\Http\Controllers\TalukashopTransferController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;

use App\Http\Controllers\TalukaToDistributionApprovalController;
use App\Http\Controllers\TalukaToTalukaApprovalController;


Route::get('/login-admin', [AdminAuthController::class, 'loginForm'])->name('login.form');
Route::post('/admin-login', [AdminAuthController::class, 'login'])->name('admin.login');

Route::post('/admin-logout', [AdminAuthController::class, 'logout'])->name('logout');
Route::post('/reset-password', [AdminAuthController::class, 'resetPassword'])
    ->name('reset.password');


Route::middleware(['auth:admin'])->group(function () 
{

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Product CRUD
    Route::resource('products', ProductController::class)
    ->middleware([
        'index' => 'permission:product.view',
        'create' => 'permission:product.create',
        'store' => 'permission:product.create',
        'edit' => 'permission:product.edit',
        'destroy' => 'permission:product.delete',
    ]);


    // USER PROFILE / ADMIN USERS (SAFE GROUPED VERSION)
    Route::prefix('user')->group(function () {

        // LIST
        Route::get('/profile', [AdminAuthController::class, 'index'])
            ->name('user.profile');
            
        // CREATE
        Route::get('/profile/create', [AdminAuthController::class, 'createUser'])
            ->name('user.create');
            

        Route::post('/profile/store', [AdminAuthController::class, 'store'])
            ->name('user.store');

        // SHOW
        Route::get('/{id}', [AdminAuthController::class, 'show'])
            ->name('user.show');

        // EDIT / UPDATE
        Route::get('/{id}/edit', [AdminAuthController::class, 'editUser'])
            ->name('user.edit');

        Route::put('/{id}', [AdminAuthController::class, 'update'])
            ->name('user.update');

        // DELETE
        Route::delete('/{id}', [AdminAuthController::class, 'destroy'])
            ->name('user.destroy');

    });


    Route::get('rolepermission', [RolePermissionController::class, 'RolePermission'])->name('RolePermission');
    Route::post('rolepermission/store', [RolePermissionController::class, 'Store'])->name('Store');
    Route::get('/get-role-permissions/{id}', [RolePermissionController::class, 'getRolePermissions']);


    Route::resource('/category', CategoryController::class);
    Route::resource('/sub-category', SubCategoryController::class);
    Route::resource('/units', UnitController::class);
    Route::resource('/product', ProductController::class);
    Route::resource('/warehouse', MasterWarehouseController::class);
    Route::resource('brands', BrandController::class);
   Route::get(
    'get-brands-by-sub-category/{subCategory}',
    [ProductController::class, 'getBrands']
);
    


    Route::post('/brand/status', [BrandController::class, 'updateStatus'])->name('updateStatus');
    // Route::get('/get-subcategories-by-category/{categoryId}', [ProductController::class, 'getSubCategoriesByCategory']);
    Route::get('get-sub-categories/{category}', [SubCategoryController::class, 'getSubCategories']);
    Route::get('/get-categories', [ProductController::class, 'getCategories']);
    Route::get('/get-sub-categories/{categoryId}', [ProductController::class, 'getSubCategories']);


    // WAREHOUSE STOCK (SAFE GROUPED)
    Route::controller(stockWarehouseController::class)->group(function () {

        // LIST
        Route::get('/index-warehouse', 'indexWarehouse')
            ->name('index.addStock.warehouse');

        // CREATE
        Route::get('/add-stock-warehouse', 'addStockForm')
            ->name('warehouse.addStockForm');

        Route::post('/add-stock-warehouse', 'addStock')
            ->name('warehouse.addStock');

        // VIEW
        Route::get('/view-stock-warehouse/{id}', 'showStockForm')
            ->name('warehouse.viewStockForm');

        // EDIT
        Route::get('/edit-stock-warehouse/{id}', 'editStockForm')
            ->name('warehouse.editStockForm');

        // UPDATE
        Route::put('/stock/{id}/update', 'updateStock')
            ->name('stock.update');

        // DELETE
        Route::delete('/stock/{id}/delete', 'destroyStock')
            ->name('stock.delete');
    });

    Route::get(
        '/get-sub-categories/{category}',
        [stockWarehouseController::class, 'byCategory']
    );
    Route::get('/get-products-by-sub-category/{subCategoryId}', [stockWarehouseController::class, 'getProductBySubCategory'])->name('stockProduct.bySubCategory');
    Route::get('/get-categories-by-warehouse/{warehouse}', [stockWarehouseController::class, 'getCategories']);


    Route::get('/get-states/{country}', [LocationController::class, 'getStates']);
    Route::get('/get-districts/{state}', [LocationController::class, 'getDistricts']);
    Route::get('/get-talukas/{district}', [LocationController::class, 'getTalukas']);


    // ROLES MANAGEMENT (SAFE GROUPED)
    Route::controller(RoleController::class)->group(function () {

        // LIST
        Route::get('/roles/index', 'index')
            ->name('roles.index');

        // CREATE
        Route::get('/roles/create', 'create')
            ->name('roles.create');

        Route::post('/roles/store', 'store')
            ->name('roles.store');

        // SHOW
        Route::get('/roles-show/{id}', 'show')
            ->name('roles.show');

        // EDIT
        Route::get('/roles/{id}/edit', 'edit')
            ->name('roles.edit');

        // UPDATE
        Route::put('/roles/update/{id}', 'update')
            ->name('roles.update');

        // DELETE (SAFE – dono rakhe gaye)
        Route::delete('/roles/{id}', 'destroy')
            ->name('roles.destroy');

        Route::get('/roles-destroy/{id}', 'destroy')
            ->name('roles.destroy');
    });


    Route::resource('/vehicle-assignments', VehicleAssignmentController::class);


    Route::resource('/delivery-agents', DeliveryAgentController::class);
    Route::post(
        '/admin-assign-delivery',
        [DeliveryAgentController::class, 'assignDelivery']
    )->name('admin.assign.delivery');


    // Deliveries List
    Route::resource('/customer-orders', CustomerOrderController::class);
    Route::resource('/customer-returns', CustomerOrderReturnController::class);


    Route::resource('/stock-returns', WarehouseStockReturnController::class);
    Route::get('/warehouse-stock-returns/{id}', [WarehouseStockReturnController::class, 'downloadPdf'])->name('warehouse-stock-returns.download-pdf');
    Route::post(
        'stock-returns/{id}/send-for-approval',
        [WarehouseStockReturnController::class, 'sendForApproval']
    )->name('stock-returns.send-for-approval');   
    Route::post('stock-returns/{id}/dispatch', [WarehouseStockReturnController::class, 'dispatch'])
        ->name('stock-returns.dispatch');
    Route::post('stock-returns/{id}/receive', [WarehouseStockReturnController::class, 'receive'])
        ->name('stock-returns.receive');
    Route::post(
        'stock-returns/{id}/close',
        [WarehouseStockReturnController::class, 'close']
    )->name('stock-returns.close');



/////////////////////////////////////////////////// SHEKHAR DEVELOPMENT ///////////////////////////////////////////////

    // WAREHOUSE TRANSFER
    Route::prefix('warehouse-transfer')->name('transfer.')->group(function ()
    {
        Route::get('/', [WarehouseTransferController::class, 'index'])->name('index');
        Route::get('/create', [WarehouseTransferController::class, 'create'])->name('create');
        Route::post('/store', [WarehouseTransferController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [WarehouseTransferController::class, 'edit'])->name('edit');
        Route::put('/{id}', [WarehouseTransferController::class, 'update'])->name('update');
        Route::delete('/{id}', [WarehouseTransferController::class, 'destroy'])->name('destroy');
    });

    Route::get(
        '/ajax/warehouse-stock-data',
        [WarehouseTransferController::class, 'getWarehouseStockData']
    )->name('ajax.warehouse.stock.data');

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

    Route::get('/ajax/transfer-qty', [WarehouseTransferController::class, 'getTransferQty'])
    ->name('ajax.transfer.qty');


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

        Route::get(
            '/get-taluka-warehouses/{district_warehouse_id}',
            [GroceryShopController::class, 'getTalukaWarehouses']
        )->name('get.taluka.warehouses');
    });

    Route::get('/talukas/by-district/{district}', [GroceryShopController::class, 'byDistrict'])
        ->name('talukas.by-district');


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

    Route::get('/sell/ws/subcategories/{warehouse}/{category}', [StockController::class, 'getSubCategories']);

    Route::get(
        '/sell/ws/products/{warehouse}/{subCategory}',
        [StockController::class, 'getProductsBySubCategory']
    )->middleware('auth');

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

    Route::post('/warehouse-transfer/{transfer}/dispatch', 
    [ApprovalController::class, 'dispatch']
    )->name('warehouse.transfer.dispatch');

    Route::post('/warehouse-transfer/{transfer}/receive', 
        [ApprovalController::class, 'receive']
    )->name('warehouse.transfer.receive');
 
    // Route::post('/warehouse-transfer/dispatch-bulk', [ApprovalController::class, 'bulkDispatch'])
    // ->name('warehouse.transfer.dispatch.bulk');

    Route::post('/warehouse-transfer/receive-bulk', 
        [ApprovalController::class, 'bulkReceive']
    )->name('warehouse.transfer.receive.bulk');


    Route::post('/warehouse-transfer/dispatch/{transfer}', 
        [ApprovalController::class, 'singleDispatch']
    )->name('warehouse.transfer.dispatch.single');

    Route::post('/warehouse-transfer/reject/{transfer}', 
        [ApprovalController::class, 'reject']
    )->name('warehouse.transfer.reject');

    Route::post('/warehouse-transfer/receive/{transfer}', 
        [ApprovalController::class, 'singleReceive']
    )->name('warehouse.transfer.receive.single');

    Route::post('/transfer-challan/dispatch', 
        [ApprovalController::class, 'dispatchChallan']
    )->name('warehouse.transfer.dispatch.bulk');



    // LOW STOCK ALERTS
    Route::get('/low-stock-alerts', [LowStockController::class, 'index'])
        ->name('lowstock.index');

    // Analytics (optional API / page)
    Route::get('/low-stock-analytics', [LowStockController::class, 'analytics'])
        ->name('lowstock.analytics');

    
    // Still Comment this module
        //  PURCHASE ORDER  
        Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase.orders.create');
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

        // Purches Order Request
        Route::prefix('warehouse-transfer-request')->group(function () {
            Route::get('/', [WarehouseTransferRequestController::class, 'index'])
                ->name('warehouse-transfer-request.index');

            Route::get('/create', [WarehouseTransferRequestController::class, 'create'])
                ->name('warehouse_transfer.create');
            Route::post('/store', [WarehouseTransferRequestController::class, 'store'])->name('warehouse-transfer-request.store');

            Route::get('/incoming', [WarehouseTransferRequestController::class, 'incoming'])->name('warehouse-transfer-request.incoming');
            Route::post('/approve/{id}', [WarehouseTransferRequestController::class, 'approve']);
            Route::post('/reject/{id}', [WarehouseTransferRequestController::class, 'reject']);
            Route::get(
                '/purchase-orders/{id}/items',
                [WarehouseTransferRequestController::class, 'items']
            );
        });
 

/////////////////////////////////////////////////////// SHEKHAR DEVELOPMENT ///////////////////////////////////////////////


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


    Route::get('warehouse-stock/report', [ReportsController::class, 'warehouse_stock_report'])
        ->name('warehouse-stock.report');
    Route::get('stock-movement/report', [ReportsController::class, 'stock_movement'])
        ->name('stock-movement.report');


    Route::group(['prefix' => 'transfer-challans', 'as' => 'transfer-challans.'], function () {

        Route::get('/', [TransferChallanController::class, 'index'])->name('index');

        Route::get('/create', [TransferChallanController::class, 'create'])->name('create');
        Route::post('/', [TransferChallanController::class, 'store'])->name('store');

        Route::get('/{transferChallan}', [TransferChallanController::class, 'show'])->name('show');
        Route::get('/{transferChallan}/edit', [TransferChallanController::class, 'edit'])->name('edit');
        Route::put('/{transferChallan}', [TransferChallanController::class, 'update'])->name('update');
        Route::delete('/{transferChallan}', [TransferChallanController::class, 'destroy'])->name('destroy');
        Route::get(
            '/{transferChallan}/download-pdf',
            [TransferChallanController::class, 'downloadPdf']
        )->name('download.pdf');

        Route::get(
            '/{transferChallan}/download-csv',
            [TransferChallanController::class, 'downloadCsv']
        )->name('download.csv');
    });


    // Taxes
    Route::prefix('settings')->group(function () {
        Route::resource('taxes', TaxController::class);
    });

    Route::get('/user-profile', function () {
        return view('profile');
    })->name('user-profile');

    Route::put('/profile/update', [UserController::class, 'updateProfile'])
        ->name('profile.update');
    Route::get('/users/profile', [UserController::class, 'profile'])
        ->name('user.profile');



/////////////////////////////////////////  SHEKHAR DEVELOP - WEBSITE START   ////////////////////////////////////////////////////////////


    // Admin contact list
    Route::prefix('contacts-details')->group(function () {
        Route::get('contacts', [BannerController::class, 'contactList'])
            ->name('admin.contacts');
    });

    // Admin - about Pages
    Route::prefix('pages')->group(function () {

        // about us
        Route::get('aboutus', [BannerController::class, 'aboutus'])
            ->name('admin.aboutus');
        Route::post('aboutus/store', [BannerController::class, 'storeAboutUs'])
            ->name('admin.aboutus.store');
    });

    // Banners admin route
    Route::prefix('banners')->group(function () {
        Route::get('/', [BannerController::class, 'index'])->name('banners.index');
        Route::get('/create', [BannerController::class, 'create'])->name('banners.create');
        Route::post('/store', [BannerController::class, 'store'])->name('banners.store');

        // same page for edit
        Route::get('/edit/{id}', [BannerController::class, 'edit'])->name('banners.edit');
        Route::post('/update/{id}', [BannerController::class, 'update'])->name('banners.update');

        Route::delete('/delete/{id}', [BannerController::class, 'destroy'])->name('banners.delete');
    });

});
// end admin auth 


// website banner route
Route::get('/', [WebsiteController::class, 'index'])->name('home');

// website contact details
Route::get('contact-details', [WebsiteController::class, 'contact'])->name('contact');
Route::post('contact-details', [WebsiteController::class, 'storeContact'])->name('contact.store');

// webiste shop page
Route::get('shop-list', [WebsiteController::class, 'shop'])->name('shop');
Route::get('/shop/filter', [WebsiteController::class, 'shopFilter'])
    ->name('shop.filter');

// website product details page
Route::get('product-details/{id}', [WebsiteController::class, 'productdetails'])
    ->name('productdetails');


// website cart 
Route::post('add-to-cart', [WebsiteController::class, 'addToCart'])
    ->name('add_cart')
    ->middleware('auth:web');

Route::get('cart', [WebsiteController::class, 'cart'])
    ->name('cart')
    ->middleware('auth:web');

Route::delete('/cart/item/{id}', [WebsiteController::class, 'removeItem'])
    ->name('remove_cart_item')
    ->middleware('auth:web');

Route::get('/checkout', [CheckoutController::class, 'index'])
    ->name('checkout')
    ->middleware('auth:web');


Route::get('/enduserlogin', [AuthController::class, 'showLogin'])->name('login');
Route::post('/enduserlogin', [AuthController::class, 'login']);

Route::get('/enduserregister', [AuthController::class, 'showRegister'])->name('register');
Route::post('/enduserregister', [AuthController::class, 'register']);

Route::post('/enduserlogout', [AuthController::class, 'websitelogout'])->name('websitelogout');

Route::post('/place-order', [CheckoutController::class, 'placeOrder'])
    ->middleware('auth');

Route::get('/orders', [CustomerOrderController::class, 'userorder'])
    ->name('userorder');

Route::post('/orders/{id}/approve', [CustomerOrderController::class, 'orderapprove'])
    ->name('orderapprove');


/////////////////////////////////////////   SHEKHAR DEVELOP - WEBSITE END   ////////////////////////////////////////////////////////////


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