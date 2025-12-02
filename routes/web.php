<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;

Route::get('/', function () {
    return view('welcome');
});



// Route::get('/login', [AdminAuthController::class, 'login']);
// Route::post('/admin/login', [AdminAuthController::class, 'login']);
