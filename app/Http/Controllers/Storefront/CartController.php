<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cart
    ) {}

    public function index(): View
    {
        return view('storefront.cart.index', [
            'items' => $this->cart->items(),
            'subtotal' => $this->cart->subtotal(),
            'total' => $this->cart->total(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::query()->active()->findOrFail($validated['product_id']);

        if (! $product->inStock()) {
            return back()->with('error', 'This product is out of stock.');
        }

        $this->cart->add($product->id, (int) ($validated['quantity'] ?? 1));

        return redirect()->route('cart.index')->with('success', 'Added to cart.');
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $this->cart->update((int) $validated['product_id'], (int) $validated['quantity']);

        return back()->with('success', 'Cart updated.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer'],
        ]);

        $this->cart->remove((int) $validated['product_id']);

        return back()->with('success', 'Item removed.');
    }
}
