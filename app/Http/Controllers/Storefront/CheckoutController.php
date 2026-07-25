<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\CheckoutRequest;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cart,
        protected OrderService $orders
    ) {}

    public function create(): View|RedirectResponse
    {
        if ($this->cart->items()->isEmpty()) {
            return redirect()->route('shop.index')->with('error', 'Your cart is empty.');
        }

        return view('storefront.checkout.create', [
            'items' => $this->cart->items(),
            'subtotal' => $this->cart->subtotal(),
            'total' => $this->cart->total(),
        ]);
    }

    public function store(CheckoutRequest $request): RedirectResponse
    {
        try {
            $order = $this->orders->placeCodOrder($request->validated());
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('checkout.success', $order->order_number);
    }

    public function success(string $orderNumber): View
    {
        $order = Order::query()
            ->with('items')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('storefront.checkout.success', compact('order'));
    }
}
