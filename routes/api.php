<?php

use App\Http\Controllers\DistrictWarehouseController;
use App\Http\Controllers\MasterWarehouseController;
use App\Http\Controllers\TalukaWarehouseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/login', [AdminAuthController::class, 'login']);
//Route::post('/admin/login', [AdminAuthController::class, 'login']);


Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');;
Route::apiResource('/master-warehouses', MasterWarehouseController::class);
Route::apiResource('/district-warehouses', DistrictWarehouseController::class);
Route::apiResource('/taluka-warehouses', TalukaWarehouseController::class);
Route::apiResource('/store', CategoryController::class);

Route::apiResource('productstore', ProductController::class);
 
 
