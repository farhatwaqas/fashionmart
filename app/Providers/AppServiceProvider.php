<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Policies\CategoryPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use App\Services\CartService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);

        View::composer(['layouts.storefront', 'layouts.admin', 'storefront.*'], function ($view) {
            $view->with('cartCount', app(CartService::class)->count());
            $view->with('storeName', Setting::getValue('store_name', 'Fashion Corner'));
            $view->with('storeTagline', Setting::getValue('store_tagline', ''));
        });
    }
}
