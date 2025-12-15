<?php

use App\Http\Controllers\DistrictWarehouseController;
use App\Http\Controllers\MasterWarehouseController;
use App\Http\Controllers\TalukaWarehouseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});
Route::post('/send-otp', [LoginController::class, 'sendOtp']);
Route::post('/verify-otp', [LoginController::class, 'verifyOtp']);
Route::post('/logout', [LoginController::class, 'verifyOtp']);

Route::apiResource('/master-warehouses', MasterWarehouseController::class);
Route::apiResource('/district-warehouses', DistrictWarehouseController::class);
Route::apiResource('/taluka-warehouses', TalukaWarehouseController::class);
Route::apiResource('/store', CategoryController::class);

Route::apiResource('/productstore', ProductController::class);
Route::apiResource('/users', UserController::class);
