<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Customer> */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => fake()->unique()->numerify('03#########'),
            'email' => fake()->optional()->safeEmail(),
            'city' => fake()->city(),
            'address' => fake()->address(),
        ];
    }
}
