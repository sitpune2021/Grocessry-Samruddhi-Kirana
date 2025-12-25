<?php


use App\Http\Controllers\DistrictWarehouseController;
use App\Http\Controllers\TalukaWarehouseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\Api\CategoryProductController;

Route::get('/', function () {
    return view('welcome');
});
Route::post('/login/{type}', [LoginController::class, 'login']);
Route::post('/forgot-password', [LoginController::class, 'forgotPassword']);
Route::post('/verify-otp/{type}', [LoginController::class, 'verifyOtp']);
Route::post('/reset-password', [LoginController::class, 'resetPassword']);
Route::middleware('auth:sanctum')->post('/logout', [LoginController::class, 'logout']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/categories', [CategoryProductController::class, 'getCategories']);
    Route::get('/categories/{id}/products', [CategoryProductController::class, 'getProductsByCategory']);
    Route::get('/brands/{id}/products', [CategoryProductController::class, 'getProductsByBrand']);
});
Route::apiResource('/district-warehouses', DistrictWarehouseController::class);
Route::apiResource('/taluka-warehouses', TalukaWarehouseController::class);

Route::apiResource('/users', UserController::class);

Route::apiResource('/batch', BatchController::class);
