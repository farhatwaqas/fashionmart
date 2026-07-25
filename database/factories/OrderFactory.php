<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Order> */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 200, 8000);

        return [
            'order_number' => Order::generateOrderNumber(),
            'customer_id' => Customer::factory(),
            'customer_name' => fake()->name(),
            'customer_phone' => fake()->numerify('03#########'),
            'customer_email' => fake()->optional()->safeEmail(),
            'city' => fake()->city(),
            'address' => fake()->address(),
            'notes' => fake()->optional()->sentence(),
            'status' => fake()->randomElement(OrderStatus::cases()),
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'subtotal' => $subtotal,
            'discount' => 0,
            'shipping' => 0,
            'total' => $subtotal,
        ];
    }
}
