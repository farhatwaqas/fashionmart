<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class CartService
{
    protected const SESSION_KEY = 'cart';

    /**
     * Cart structure: [product_id => ['product_id' => int, 'quantity' => int]]
     */
    public function all(): array
    {
        return session()->get(self::SESSION_KEY, []);
    }

    public function count(): int
    {
        return collect($this->all())->sum('quantity');
    }

    public function add(int $productId, int $quantity = 1): void
    {
        $cart = $this->all();
        $quantity = max(1, $quantity);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'product_id' => $productId,
                'quantity' => $quantity,
            ];
        }

        $product = Product::query()->find($productId);
        if ($product) {
            $cart[$productId]['quantity'] = min($cart[$productId]['quantity'], max(1, $product->quantity));
        }

        session()->put(self::SESSION_KEY, $cart);
    }

    public function update(int $productId, int $quantity): void
    {
        $cart = $this->all();

        if (! isset($cart[$productId])) {
            return;
        }

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $product = Product::query()->find($productId);
            $max = $product ? max(1, $product->quantity) : $quantity;
            $cart[$productId]['quantity'] = min($quantity, $max);
        }

        session()->put(self::SESSION_KEY, $cart);
    }

    public function remove(int $productId): void
    {
        $cart = $this->all();
        unset($cart[$productId]);
        session()->put(self::SESSION_KEY, $cart);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    /**
     * @return Collection<int, object>
     */
    public function items(): Collection
    {
        $cart = $this->all();

        if ($cart === []) {
            return collect();
        }

        $products = Product::query()
            ->with(['coverImage', 'images'])
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        return collect($cart)->map(function (array $row) use ($products) {
            $product = $products->get($row['product_id']);
            if (! $product) {
                return null;
            }

            $qty = (int) $row['quantity'];
            $line = (float) $product->price * $qty;

            return (object) [
                'product' => $product,
                'quantity' => $qty,
                'unit_price' => (float) $product->price,
                'line_total' => $line,
            ];
        })->filter()->values();
    }

    public function subtotal(): float
    {
        return (float) $this->items()->sum('line_total');
    }

    /**
     * Future-ready coupon hook. Returns discount amount.
     */
    public function discount(?string $couponCode = null): float
    {
        // Coupon architecture placeholder — returns 0 until coupons are implemented.
        return 0.0;
    }

    public function total(?string $couponCode = null): float
    {
        return max(0, $this->subtotal() - $this->discount($couponCode));
    }
}
