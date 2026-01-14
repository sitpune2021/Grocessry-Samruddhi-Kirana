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
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\NotificationController;

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

Route::middleware('auth:sanctum')->group(function () {

    // Customer Address APIs
    Route::get('customer/addresses', [AddressController::class, 'list']);
    Route::post('customer/addresses', [AddressController::class, 'add']);
    Route::put('customer/addresses/{id}', [AddressController::class, 'update']);
    Route::delete('customer/addresses/{id}', [AddressController::class, 'delete']);
});

//---------------------Delivery Agent Api Routes-----------------------------------------------
Route::prefix('auth')->group(function () {
    Route::post('mobile/verify-otp/{type}', [DeliveryAgentController::class, 'verifyOtp']);
    Route::post('mobile/resend-otp', [DeliveryAgentController::class, 'resendOtp']);
    Route::post('/login/{type}', [DeliveryAgentController::class, 'login']);
    Route::post('/reset-password', [DeliveryAgentController::class, 'resetPassword']);
    Route::post('forgot-password/send-otp', [DeliveryAgentController::class, 'forgotPasswordSendOtp']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [DeliveryAgentController::class, 'logout']);
    Route::post('partner/status/online', [DeliveryAgentController::class, 'goOnline']);
    Route::post('partner/status/offline', [DeliveryAgentController::class, 'goOffline']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/delivery/orders/new', [DeliveryOrderController::class, 'getNewOrders']);
    Route::post('/delivery/orders/{order}/accept', [DeliveryOrderController::class, 'acceptOrder']);
    Route::post('/delivery/orders/{order}/reject', [DeliveryOrderController::class, 'rejectOrder']);
    Route::get('/delivery/orders/available', [DeliveryOrderController::class, 'getAvailableOrders']);
    Route::get('/delivery/orders/queue', [DeliveryOrderController::class, 'getDeliveryQueue']);
    Route::get('/orders/{orderId}', [DeliveryOrderController::class, 'getOrderDetails']);
    Route::get('orders/{orderId}/items', [DeliveryOrderController::class, 'getOrderItems']);
    Route::post('/orders/{orderId}/instructions/read', [DeliveryOrderController::class, 'confirmInstructionsRead']);
    Route::get('/orders/{orderId}/pickup', [DeliveryOrderController::class, 'getPickupDetails']);
    Route::post('/orders/{orderId}/items/{itemId}/verify', [DeliveryOrderController::class, 'verifyItem']);
    Route::post('/orders/{orderId}/items/{itemId}/issue', [DeliveryOrderController::class, 'reportItemIssue']);
    Route::post('/orders/{orderId}/pickup-proof', [DeliveryOrderController::class, 'uploadPickupProof']);
    Route::post('/orders/{orderId}/pickup/complete', [DeliveryOrderController::class, 'confirmPickup']);
    Route::get('/orders/cancellation-reasons', [DeliveryOrderController::class, 'getCancellationReasons']);
    Route::post('/orders/{orderId}/cancel', [DeliveryOrderController::class, 'cancelOrder']);
    Route::get('/deliveries', [DeliveryOrderController::class, 'myDeliveries']);
    Route::get('/deliveries/search', [DeliveryOrderController::class, 'search']);
    Route::get('/deliveries/status', [DeliveryOrderController::class, 'status']);
    Route::get('/orders/{orderId}/summary', [DeliveryOrderController::class, 'getOrderSummary']);
    Route::post('/orders/{orderId}/complete', [DeliveryOrderController::class, 'completeOrder']);
    Route::post('/orders/{orderId}/rate-customer', [DeliveryOrderController::class, 'rateCustomer']);
    Route::get('/partner/status/orders', [DeliveryOrderController::class, 'totalOrders']);
    Route::get('/partner/status/online-status',[DeliveryAgentController::class, 'onlineStatus']);
    Route::get('/partner/stats/login-hours',[DeliveryAgentController::class, 'loginHours']);
    Route::get('/notifications', [NotificationController::class, 'get_notifications']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);
    Route::get('/notifications/settings', [NotificationController::class, 'getSettings']);
    Route::put('/notifications/settings', [NotificationController::class, 'updateSettings']);
    Route::get('/delivery_boy/profile', [DeliveryAgentController::class, 'profile']);

    Route::put('/delivery_boy/profile/vehicle', [DeliveryAgentController::class, 'updateVehicle']);
    Route::put(
        '/delivery_boy/profile/{type}',
        [DeliveryAgentController::class, 'updateProfileField']
    );
});
Route::middleware('auth:sanctum')->group(function () {

    Route::put('/partner/profile/address', [DeliveryAgentController::class, 'updateAddress']);
    Route::post('/partner/profile/image', [DeliveryAgentController::class, 'updateProfileImage']);
});
