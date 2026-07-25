<?php

namespace Database\Factories;

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Product> */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('###'),
            'sku' => strtoupper(fake()->bothify('FC-####??')),
            'description' => fake()->paragraphs(2, true),
            'short_description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 50, 5000),
            'old_price' => fake()->optional(0.4)->randomFloat(2, 5100, 8000),
            'quantity' => fake()->numberBetween(0, 100),
            'featured' => fake()->boolean(20),
            'hot_selling' => fake()->boolean(25),
            'recommended' => fake()->boolean(25),
            'status' => ProductStatus::Active,
            'meta_title' => Str::title($name).' | Fashion Corner',
            'meta_description' => fake()->sentence(),
        ];
    }
}
