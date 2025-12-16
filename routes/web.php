<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterWarehouseController;
use App\Http\Controllers\ProductController;

Route::get('/', [AdminAuthController::class, 'loginForm'])->name('login.form');
Route::post('/admin-login', [AdminAuthController::class, 'login'])->name('admin.login');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('logout');
});
// Route::get('/', [AdminAuthController::class, 'loginForm'])->name('login.form');
// Route::post('/admin-login', [AdminAuthController::class, 'login'])->name('admin.login');
// Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('logout');
// Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


Route::resource('/category', CategoryController::class);
Route::resource('/product', ProductController::class);
Route::resource('/warehouse', MasterWarehouseController::class);
