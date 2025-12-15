<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;

Route::get('/', [AdminAuthController::class, 'loginForm'])->name('login.form');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::resource('/category', CategoryController::class);

// Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/store', [CategoryController::class, 'store'])->name('store');
Route::post('/edit', [CategoryController::class, 'edit'])->name('edit');
