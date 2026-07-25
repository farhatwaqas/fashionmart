<?php

use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\ProductController as StorefrontProductController;
use App\Http\Controllers\Storefront\ShopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront
|--------------------------------------------------------------------------
*/
Route::get('/', HomeController::class)->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/category/{category:slug}', [ShopController::class, 'category'])->name('shop.category');
Route::get('/product/{product:slug}', [StorefrontProductController::class, 'show'])->name('product.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->middleware('throttle:30,1')->name('cart.store');
Route::patch('/cart', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart', [CartController::class, 'destroy'])->name('cart.destroy');

Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:10,1')->name('checkout.store');
Route::get('/checkout/success/{orderNumber}', [CheckoutController::class, 'success'])->name('checkout.success');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::resource('products', AdminProductController::class)->except(['show']);
        Route::delete('products/{product}/images/{image}', [AdminProductController::class, 'destroyImage'])
            ->name('products.images.destroy');
        Route::post('products/{product}/images/reorder', [AdminProductController::class, 'reorderImages'])
            ->name('products.images.reorder');

        Route::resource('categories', AdminCategoryController::class)->except(['show']);
        Route::resource('banners', BannerController::class)->except(['show']);

        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
        Route::get('orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');

        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

        Route::get('backup', [BackupController::class, 'index'])->name('backup.index');
        Route::get('backup/products', [BackupController::class, 'exportProducts'])->name('backup.products');
        Route::get('backup/categories', [BackupController::class, 'exportCategories'])->name('backup.categories');
        Route::get('backup/orders', [BackupController::class, 'exportOrders'])->name('backup.orders');
        Route::post('backup/mysql', [BackupController::class, 'mysqlDump'])->name('backup.mysql');
        Route::get('backup/download/{file}', [BackupController::class, 'download'])->name('backup.download');
    });

require __DIR__.'/auth.php';
