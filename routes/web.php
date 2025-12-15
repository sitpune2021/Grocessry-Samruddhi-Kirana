<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterWarehouseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductBatchController;
use App\Http\Controllers\StockController;

Route::get('/', [AdminAuthController::class, 'loginForm'])->name('login.form');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('/category', CategoryController::class);
Route::resource('/product', ProductController::class);
Route::resource('/warehouse', MasterWarehouseController::class);


Route::get('/batches', [ProductBatchController::class, 'index'])->name('batches.index');
Route::get('/batches/create', [ProductBatchController::class, 'create']);
Route::post('/batches', [ProductBatchController::class, 'store']);


Route::get('/sale/{product}', [StockController::class, 'create'])->name('sale.create');
Route::post('/sale', [StockController::class, 'store']);

Route::get('/expiry-alerts', [ProductBatchController::class, 'expiryAlerts']);

