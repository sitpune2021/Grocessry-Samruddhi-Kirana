<?php


use App\Http\Controllers\DistrictWarehouseController;
use App\Http\Controllers\TalukaWarehouseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\Api\CategoryProductController;
use App\Http\Controllers\Api\DeliveryAgentController;
use App\Http\Controllers\Api\DeliveryOrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\DeliveryCouponsOffersController;

Route::post('/register', [LoginController::class, 'register']);
Route::post('/login/{type}', [LoginController::class, 'login']);
Route::post('/forgot-password', [LoginController::class, 'forgotPassword']);
Route::post('/verify-otp/{type}', [LoginController::class, 'verifyOtp']);
Route::post('/reset-password', [LoginController::class, 'resetPassword']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
});
Route::post('/customer/update-profile', [LoginController::class, 'updateProfile'])
    ->middleware('auth:sanctum');

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::get('/categories',                    [CategoryProductController::class, 'getCategories']);
    Route::get('/categories/{id}/subcategories', [CategoryProductController::class, 'getSubCategoriesByCategory']);
    Route::get('/subcategories/{id}/products',   [CategoryProductController::class, 'getProductsBySubcategory']);
    Route::get('/brands',                        [CategoryProductController::class, 'getBrands']);
    Route::get('/brands/{id}/products',          [CategoryProductController::class, 'getProductsByBrand']);
    Route::get('/products/{id}/similar',         [CategoryProductController::class, 'getSimilarProducts']);
    Route::get('/products/{id}',                 [CategoryProductController::class, 'getProductDetails']);
});

Route::apiResource('/district-warehouses',  DistrictWarehouseController::class);
Route::apiResource('/taluka-warehouses',    TalukaWarehouseController::class);
Route::apiResource('/users',                UserController::class);
Route::apiResource('/batch',                BatchController::class);

// Route::post('/cart/add', [ProductController::class, 'addToCart']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/cart/add', [ProductController::class, 'addToCart']);
    Route::get('/cart', [ProductController::class, 'viewCart']);
    Route::delete('/cart/clear', [ProductController::class, 'clearCart']);
    Route::delete('cart/single/product/remove', [ProductController::class, 'removeSingleItem']);
    Route::post('/cart/checkout', [ProductController::class, 'checkout']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/offers', [DeliveryCouponsOffersController::class, 'getOffers']);
    Route::post('/apply-coupon', [DeliveryCouponsOffersController::class, 'applyCoupon']);
    Route::post('/remove-coupon', [DeliveryCouponsOffersController::class, 'removeCoupon']);
});

//---------------------Delivery Agent Api Routes-----------------------------------------------
Route::prefix('auth')->group(function () {
    Route::post('mobile/verify-otp/{type}', [DeliveryAgentController::class, 'verifyOtp']);
    Route::post('mobile/resend-otp', [DeliveryAgentController::class, 'resendOtp']);
    Route::post('/login/{type}', [DeliveryAgentController::class, 'login']);
    Route::post('/reset-password', [DeliveryAgentController::class, 'resetPassword']);
    Route::post('forgot-password/send-otp', [DeliveryAgentController::class, 'forgotPasswordSendOtp']);
    Route::middleware('auth:sanctum')->post('logout', [DeliveryAgentController::class, 'logout']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/partner/status/online', [DeliveryAgentController::class, 'goOnline']);
    Route::post('/partner/status/offline', [DeliveryAgentController::class, 'goOffline']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/delivery/orders/new', [DeliveryOrderController::class, 'getNewOrders']);
});
