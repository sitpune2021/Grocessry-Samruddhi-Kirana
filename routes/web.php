<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CategoryController;

Route::get('/', function () {
    return view(view: 'welcome');
});



// Route::get('/login', [AdminAuthController::class, 'login']);
// Route::post('/admin/login', [AdminAuthController::class, 'login']);
Route::post('/store', [CategoryController::class, 'store'])->name('store');
Route::post('/edit', [CategoryController::class, 'edit'])->name('edit');
