<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::query()->active()->orderBy('sort_order')->get();

        $products = Product::query()
            ->active()
            ->with(['coverImage', 'images', 'category'])
            ->when($request->filled('category'), function ($q) use ($request) {
                $q->whereHas('category', fn ($c) => $c->where('slug', $request->string('category')));
            })
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->string('q').'%';
                $q->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', $term)
                        ->orWhere('short_description', 'like', $term)
                        ->orWhere('sku', 'like', $term);
                });
            })
            ->when($request->filled('min_price'), fn ($q) => $q->where('price', '>=', (float) $request->input('min_price')))
            ->when($request->filled('max_price'), fn ($q) => $q->where('price', '<=', (float) $request->input('max_price')))
            ->when($request->input('sort') === 'price_asc', fn ($q) => $q->orderBy('price'))
            ->when($request->input('sort') === 'price_desc', fn ($q) => $q->orderByDesc('price'))
            ->when($request->input('sort') === 'newest' || ! $request->filled('sort'), fn ($q) => $q->latest())
            ->when($request->input('sort') === 'name', fn ($q) => $q->orderBy('name'))
            ->paginate(12)
            ->withQueryString();

        return view('storefront.shop.index', compact('products', 'categories'));
    }

    public function category(Category $category): View
    {
        abort_unless($category->is_active, 404);

        request()->merge(['category' => $category->slug]);

        return $this->index(request());
    }
}
