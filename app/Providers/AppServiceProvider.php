<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use App\Models\Product;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ✅ THIS MUST BE HERE (GLOBAL)
        Schema::defaultStringLength(191);

        // Sidebar products
        View::composer('layouts.sidebar', function ($view) {
            $view->with('products', Product::all());
        });
    }
}
