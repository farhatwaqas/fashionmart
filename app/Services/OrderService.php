<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OrderService
{
    public function __construct(
        protected CartService $cart
    ) {}

    /**
     * @param  array{name: string, phone: string, email?: ?string, city: string, address: string, notes?: ?string}  $customerData
     */
    public function placeCodOrder(array $customerData): Order
    {
        $items = $this->cart->items();

        if ($items->isEmpty()) {
            throw new InvalidArgumentException('Your cart is empty.');
        }

        return DB::transaction(function () use ($customerData, $items) {
            foreach ($items as $item) {
                /** @var Product $product */
                $product = Product::query()->lockForUpdate()->findOrFail($item->product->id);

                if ($product->quantity < $item->quantity) {
                    throw new InvalidArgumentException("Insufficient stock for {$product->name}.");
                }
            }

            $customer = Customer::query()->updateOrCreate(
                ['phone' => $customerData['phone']],
                [
                    'name' => $customerData['name'],
                    'email' => $customerData['email'] ?? null,
                    'city' => $customerData['city'],
                    'address' => $customerData['address'],
                ]
            );

            $subtotal = $this->cart->subtotal();
            $discount = $this->cart->discount();
            $total = $this->cart->total();

            $order = Order::query()->create([
                'order_number' => Order::generateOrderNumber(),
                'customer_id' => $customer->id,
                'customer_name' => $customerData['name'],
                'customer_phone' => $customerData['phone'],
                'customer_email' => $customerData['email'] ?? null,
                'city' => $customerData['city'],
                'address' => $customerData['address'],
                'notes' => $customerData['notes'] ?? null,
                'status' => OrderStatus::Pending,
                'payment_method' => 'cod',
                'payment_status' => 'pending',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping' => 0,
                'total' => $total,
            ]);

            foreach ($items as $item) {
                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'product_name' => $item->product->name,
                    'product_sku' => $item->product->sku,
                    'unit_price' => $item->unit_price,
                    'quantity' => $item->quantity,
                    'line_total' => $item->line_total,
                ]);

                Product::query()->where('id', $item->product->id)->decrement('quantity', $item->quantity);
            }

            $this->cart->clear();

            return $order->load('items');
        });
    }

    public function updateStatus(Order $order, OrderStatus $status): Order
    {
        $previousStatus = $order->getRawOriginal('status');

        $order->status = $status;

        match ($status) {
            OrderStatus::Confirmed => $order->confirmed_at = $order->confirmed_at ?? now(),
            OrderStatus::Packed => $order->packed_at = $order->packed_at ?? now(),
            OrderStatus::Shipped => $order->shipped_at = $order->shipped_at ?? now(),
            OrderStatus::Delivered => $order->delivered_at = $order->delivered_at ?? now(),
            OrderStatus::Cancelled => $order->cancelled_at = $order->cancelled_at ?? now(),
            default => null,
        };

        // Restock inventory when cancelling a previously active order
        if ($status === OrderStatus::Cancelled && $previousStatus !== OrderStatus::Cancelled->value) {
            $order->loadMissing('items');
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    Product::query()->where('id', $item->product_id)->increment('quantity', $item->quantity);
                }
            }
        }

        $order->save();

        return $order;
    }
}
