<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductReview>
 */
class ProductReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'        => User::all()->random()->id,
            'product_id'     => Product::all()->random()->id,
            'order_id'       => Order::all()->random()->id,
            'rating'         => $this->faker->numberBetween(1, 5),
            'parent_id'      => null,
            'comment'        => $this->faker->sentence(10),
            'images'         => null,
            'publish'        => $this->faker->numberBetween(1, 2),

        ];
    }
}
