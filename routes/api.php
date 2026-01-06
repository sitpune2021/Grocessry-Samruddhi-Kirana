<?php


use App\Http\Controllers\DistrictWarehouseController;
use App\Http\Controllers\TalukaWarehouseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\Api\CategoryProductController;
use App\Http\Controllers\Api\DeliveryAgentController;

// Route::get('/', function () {
//     return view('welcome');
// });

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

    Route::prefix('auth')->group(function () {

        // 📱 Mobile OTP
        Route::post('mobile/send-otp',   [DeliveryAgentController::class, 'sendOtp']);
        Route::post('mobile/verify-otp', [DeliveryAgentController::class, 'verifyOtp']);
        Route::post('mobile/resend-otp', [DeliveryAgentController::class, 'resendOtp']);

        // 📧 Email Login
        Route::post('email/login', [DeliveryAgentController::class, 'emailLogin']);

        // 🔐 Logout (Protected)
        Route::middleware('auth:sanctum')->post('logout', [DeliveryAgentController::class, 'logout']);
    });
