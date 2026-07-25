<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $banners = Banner::query()->active()->orderBy('sort_order')->get();
        $categories = Category::query()->active()->withCount(['products' => fn ($q) => $q->active()])->orderBy('sort_order')->get();

        $featured = Product::query()->active()->featured()->with(['coverImage', 'images', 'category'])->latest()->limit(8)->get();
        $hotSelling = Product::query()->active()->hotSelling()->with(['coverImage', 'images', 'category'])->latest()->limit(8)->get();
        $recommended = Product::query()->active()->recommended()->with(['coverImage', 'images', 'category'])->latest()->limit(8)->get();
        $newArrivals = Product::query()->active()->with(['coverImage', 'images', 'category'])->latest()->limit(8)->get();

        return view('storefront.home.index', compact(
            'banners',
            'categories',
            'featured',
            'hotSelling',
            'recommended',
            'newArrivals'
        ));
    }
}
