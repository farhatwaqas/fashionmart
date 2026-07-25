<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $products = Product::query()->active()->latest('updated_at')->get(['slug', 'updated_at']);
        $categories = Category::query()->active()->get(['slug', 'updated_at']);

        $xml = view('sitemap', compact('products', 'categories'))->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
