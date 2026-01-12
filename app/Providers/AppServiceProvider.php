<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use App\Models\Product;
use Illuminate\Support\Facades\Blade;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;

class AppServiceProvider extends ServiceProvider
{
    
    public function register(): void
    {
        //
    }

    // public function boot()
    // {
    //     Paginator::useBootstrap();
        
    //     // Fix for MySQL key length issue (utf8mb4)
    //     Schema::defaultStringLength(191);
        

    //     Blade::if('canPermission', function ($permission) {

    //         $user = auth()->user();
 
    //         return $user && method_exists($user, 'hasPermission')
    //             && $user->hasPermission($permission);
    //     });
    // }

   
    public function boot()
    {
        Paginator::useBootstrap();
        Schema::defaultStringLength(191);

        Blade::if('canPermission', function ($permission) {
            $user = auth()->user();
            return $user && method_exists($user, 'hasPermission')
                && $user->hasPermission($permission);
        });

        // ✅ Cart Count Share (NEW)
        View::composer('*', function ($view) {
            $cartCount = 0;

            if (Auth::check()) {
                $cart = Cart::where('user_id', Auth::id())->with('items')->first();
            } else {
                $cart = Cart::where('user_id', session()->getId())->with('items')->first();
            }

            if ($cart) {
                $cartCount = $cart->items->sum('qty');
            }

            $view->with('cartCount', $cartCount);
        });
    }


}
