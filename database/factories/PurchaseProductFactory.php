<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class PurchaseProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'slug' => fake()->unique()->slug(),
            'sku' => fake()->unique()->bothify('QA-########'),
            'price' => '1000.00',
            'old_price' => '1200.00',
            'stock' => 10,
            'status' => true,
            'has_variant' => false,
            'installment_enabled' => false,
            'short_description' => 'Test product description.',
        ];
    }
}
