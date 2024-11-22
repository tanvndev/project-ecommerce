<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductView>
 */
class ProductViewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'            => User::all()->random()->id,
            'product_variant_id' => ProductVariant::all()->random()->id,
            'viewed_at'          => fake()->dateTimeBetween('-3 month', '-2 month'),
        ];
    }
}
