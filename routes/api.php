<?php

use App\Http\Controllers\DistrictWarehouseController;
use App\Http\Controllers\TalukaWarehouseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\Api\CategoryProductController;
use App\Http\Controllers\Api\DeliveryAgentController;
use App\Http\Controllers\Api\CustomerProductReturnController;
use App\Http\Controllers\Api\DeliveryOrderController;



use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CustomerCouponsOffersController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\DeliveryAgentDutyController;
use App\Http\Controllers\Api\DeliveryPartnerReturnController;

Route::post('/register', [LoginController::class, 'register']);
Route::post('/login/{type}', [LoginController::class, 'login']);
Route::post('/forgot-password', [LoginController::class, 'forgotPassword']);
Route::post('/verify-otp/{type}', [LoginController::class, 'verifyOtp']);
Route::post('/reset-password', [LoginController::class, 'resetPassword']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/profile', [LoginController::class, 'userprofiledetails']);
    Route::delete('/user/account', [LoginController::class, 'deleteAccount']);
});
Route::post('/customer/update-profile', [LoginController::class, 'updateProfile'])
    ->middleware('auth:sanctum');
Route::get('/customer/order-time-check', [LoginController::class, 'orderTimeCheck'])->middleware('auth:sanctum');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/products/search', [ProductController::class, 'search']);
    Route::get('/categories',                    [CategoryProductController::class, 'getCategories']);
    Route::get('/categories/{id}/subcategories', [CategoryProductController::class, 'getSubCategoriesByCategory']);
    Route::get('/subcategories/{id}/products',   [CategoryProductController::class, 'getProductsBySubcategory']);
    Route::get('/brands',                        [CategoryProductController::class, 'getBrands']);
    Route::get('/brands/{id}/products',          [CategoryProductController::class, 'getProductsByBrand']);
    Route::get('/products/{id}/similar',         [CategoryProductController::class, 'getSimilarProducts']);
    Route::get('/products/{id}',                 [CategoryProductController::class, 'getProductDetails']);
    Route::get('/brands/{brand_id}/products', [CategoryProductController::class, 'productsByBrand']);
    Route::get('/banners', [CategoryProductController::class, 'getBanners']);
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
    Route::post('/customer/product/return', [ProductController::class, 'returnProduct']);
    Route::get('/orders/history', [ProductController::class, 'pastOrders']);
    Route::get('/orders/new-order', [ProductController::class, 'newOrders']);
    Route::post('/orders/{orderId}/rate-product', [ProductController::class, 'rateOrder']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/cart/increment', [ProductController::class, 'incrementCart']);
    Route::post('/cart/decrement', [ProductController::class, 'decrementCart']);
    Route::delete('/cart/remove', [ProductController::class, 'removeFromCart']);
    Route::get('/cart', [ProductController::class, 'viewCart']);
    Route::post('cart/brand/add', [ProductController::class, 'addBrandProductToCart']);
    Route::post('api/cart/increment', [ProductController::class, 'incrementCartItem']);
    Route::post('api/cart/decrement', [ProductController::class, 'decrementCartItem']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/offers', [CustomerCouponsOffersController::class, 'getOffers']);
    Route::post('/apply-offer', [CustomerCouponsOffersController::class, 'applyOffer']);
    Route::post('/remove-offer', [CustomerCouponsOffersController::class, 'removeOffer']);
    Route::get('/coupons', [CustomerCouponsOffersController::class, 'getAllCoupons']);
    Route::post('/cart/apply-coupon', [CustomerCouponsOffersController::class, 'applyCoupon']);
    Route::post('/cart/remove-coupon', [CustomerCouponsOffersController::class, 'removeCoupon']);
});

Route::middleware('auth:sanctum')->group(function () {

    // Customer Address APIs
    Route::get('customer/addresses', [AddressController::class, 'list']);
    Route::post('customer/addresses', [AddressController::class, 'add']);
    Route::put('customer/addresses/{id}', [AddressController::class, 'update']);
    Route::delete('customer/addresses/{id}', [AddressController::class, 'delete']);
    Route::post('/user/address/set-default', [AddressController::class, 'setDefault']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('return/reasons', [CustomerProductReturnController::class, 'returnReasons']);
    Route::post('order/return', [CustomerProductReturnController::class, 'createReturn']);
    // Route::get(
    //     'order/{order_id}/product-stock',
    //     [CustomerProductReturnController::class, 'orderProductStock']
    // );
    Route::get(
        '/orders/{order_id}/return-products',
        [CustomerProductReturnController::class, 'getOrderReturnProducts']
    );
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
    Route::get('/partner/profile/image', [DeliveryAgentController::class, 'getProfileImage']);
});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/partner/status/online-status', [DeliveryAgentDutyController::class, 'onlineStatus']);
    Route::get('/partner/stats/login-hours', [DeliveryAgentDutyController::class, 'loginHours']);
    Route::post('partner/status/online', [DeliveryAgentDutyController::class, 'goOnline']);
    Route::post('partner/status/offline', [DeliveryAgentDutyController::class, 'goOffline']);
    Route::get('/partner/orders/current', [DeliveryAgentDutyController::class, 'getCurrentTask']);
    Route::get('/partner/profile/summary', [DeliveryAgentDutyController::class, 'profileSummary']);
    Route::get('/partner/performance/graph', [DeliveryAgentDutyController::class, 'performanceGraph']);
    Route::post('/partner/duty/start', [DeliveryAgentDutyController::class, 'startDuty']);
    Route::post('/partner/duty/pause', [DeliveryAgentDutyController::class, 'pauseDuty']);
    Route::post('/partner/duty/resume', [DeliveryAgentDutyController::class, 'resumeDuty']);
    Route::post('/partner/duty/stop', [DeliveryAgentDutyController::class, 'stopDuty']);
    Route::get('/partner/profile/summary', [DeliveryAgentDutyController::class, 'partnerSummary']);
    Route::post('partner/orders/{orderId}/delivery/verify-otp', [DeliveryAgentDutyController::class, 'verifyDeliveryOtp']);
    Route::post('/partner/orders/{orderId}/delivery/{type}-otp', [DeliveryAgentDutyController::class, 'deliveryOtp']);
    Route::post('/partner/duty/reset', [DeliveryAgentDutyController::class, 'resetDailyDuty']);
    Route::get('/partner/performance/day', [DeliveryAgentDutyController::class, 'dayPerformance']);
    Route::get('/partner/performance/week', [DeliveryAgentDutyController::class, 'weekPerformance']);
    Route::get('/partner/performance/month', [DeliveryAgentDutyController::class, 'monthPerformance']);
});
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/delivery/orders/new', [DeliveryOrderController::class, 'getNewOrders']);
    Route::get('/delivery/orders/available', [DeliveryOrderController::class, 'getAvailableOrders']);
    Route::get('/delivery/orders/queue', [DeliveryOrderController::class, 'getDeliveryQueue']);

    Route::get('/orders/cancellation-reasons', [DeliveryOrderController::class, 'getCancellationReasons']);

    Route::post('/orders/{orderId}/complete', [DeliveryOrderController::class, 'completeOrder']);
    Route::post('/orders/{orderId}/pending', [DeliveryOrderController::class, 'markPending']);
    Route::get('/orders/pending', [DeliveryOrderController::class, 'getPendingOrders']);
    Route::post('/orders/{orderId}/resume', [DeliveryOrderController::class, 'resumeOrder']);
    Route::post('/delivery/orders/{orderId}/accept', [DeliveryOrderController::class, 'acceptOrder']);
    Route::post('/delivery/orders/{orderId}/reject', [DeliveryOrderController::class, 'rejectOrder']);
    Route::post('/orders/{orderId}/start', [DeliveryOrderController::class, 'startOrder']);
    // Route::get('/orders/{orderId}/items', [DeliveryOrderController::class, 'getOrderItems']);
    Route::get('/partner/orders/{orderId}/items', [DeliveryOrderController::class, 'getPickupItems']);
    Route::get(
        '/partner/orders/{orderId}/items',
        [DeliveryOrderController::class, 'getOrderItems']
    );
    Route::get('/orders/{orderId}/pickup', [DeliveryOrderController::class, 'getPickupDetails']);
    Route::post('/orders/{orderId}/pickup-proof', [DeliveryOrderController::class, 'uploadPickupProof']);
    Route::post('/orders/{orderId}/pickup/complete', [DeliveryOrderController::class, 'confirmPickup']);

    Route::post('/orders/{orderId}/items/{itemId}/verify', [DeliveryOrderController::class, 'verifyItem']);
    Route::post('/orders/{orderId}/items/{itemId}/issue', [DeliveryOrderController::class, 'reportItemIssue']);

    Route::post('/orders/{orderId}/cancel', [DeliveryOrderController::class, 'cancelOrder']);

    Route::get('/orders/{orderId}', [DeliveryOrderController::class, 'getOrderDetails']);
    Route::get('/orders/{orderId}/summary', [DeliveryOrderController::class, 'getOrderSummary']);
    Route::post('/orders/{orderId}/complete', [DeliveryOrderController::class, 'completeOrder']);
    Route::post('/orders/{orderId}/rate-customer', [DeliveryOrderController::class, 'rateCustomer']);

    Route::get('/deliveries', [DeliveryOrderController::class, 'myDeliveries']);
    Route::get('/deliveries/search', [DeliveryOrderController::class, 'search']);
    Route::get('/deliveries/status', [DeliveryOrderController::class, 'status']);
    Route::get('/partner/deliveries/summary', [DeliveryOrderController::class, 'deliverySummary']);
    Route::get('/orders/{orderId}/items', [DeliveryOrderController::class, 'getPickupItems']);
    Route::get('/partner/status/orders', [DeliveryOrderController::class, 'totalOrders']);

    Route::get('/notifications', [NotificationController::class, 'get_notifications']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);
    Route::get('/notifications/settings', [NotificationController::class, 'getSettings']);
    Route::put('/notifications/settings', [NotificationController::class, 'updateSettings']);

    Route::get('/delivery_boy/profile', [DeliveryAgentController::class, 'profile']);
    Route::put('/delivery_boy/profile/vehicle', [DeliveryAgentController::class, 'updateVehicle']);
    Route::put('/delivery_boy/profile/{type}', [DeliveryAgentController::class, 'updateProfileField']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::put('/partner/profile/address', [DeliveryAgentController::class, 'updateAddress']);
    Route::post('/partner/profile/image', [DeliveryAgentController::class, 'updateProfileImage']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/v1/partner/returns/{returnId}/start', [DeliveryPartnerReturnController::class, 'startReturn']);
    Route::post('/v1/partner/returns/{returnId}/arrive', [DeliveryPartnerReturnController::class, 'arriveAtStore']);
    Route::get('/v1/partner/returns/{returnId}/receipt', [DeliveryPartnerReturnController::class, 'printReceipt']);
    Route::post('/v1/partner/returns/{returnId}/handover', [DeliveryPartnerReturnController::class, 'confirmHandover']);
    Route::get('/v1/partner/returns/{returnId}', [DeliveryPartnerReturnController::class, 'getHandoverDetails']);
});
