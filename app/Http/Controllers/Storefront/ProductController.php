<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Product $product): View
    {
        abort_unless($product->status->value === 'active', 404);

        $product->load(['images', 'category', 'coverImage']);

        $related = Product::query()
            ->active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['coverImage', 'images'])
            ->limit(4)
            ->get();

        if ($related->count() < 4) {
            $extra = Product::query()
                ->active()
                ->recommended()
                ->where('id', '!=', $product->id)
                ->whereNotIn('id', $related->pluck('id'))
                ->with(['coverImage', 'images'])
                ->limit(4 - $related->count())
                ->get();
            $related = $related->concat($extra);
        }

        return view('storefront.product.show', compact('product', 'related'));
    }
}
