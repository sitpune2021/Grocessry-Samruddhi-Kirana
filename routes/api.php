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
use App\Http\Controllers\BatchController;

Route::get('/', function () {
    return view('welcome');
});
Route::post('/send-otp', [LoginController::class, 'sendOtp']);
Route::post('/verify-otp', [LoginController::class, 'verifyOtp']);
Route::post('/logout', [LoginController::class, 'verifyOtp']);


Route::apiResource('/district-warehouses', DistrictWarehouseController::class);
Route::apiResource('/taluka-warehouses', TalukaWarehouseController::class);

Route::apiResource('/users', UserController::class);


Route::apiResource('/batch', BatchController::class);
