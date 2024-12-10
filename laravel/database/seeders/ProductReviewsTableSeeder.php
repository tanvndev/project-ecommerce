<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory as Faker;

class ProductReviewsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 100; $i++) { // Tạo 100 bản ghi
            DB::table('product_reviews')->insert([
                'product_id' => rand(27, 52),
                'user_id' => rand(22, 200),
                // 'order_id' => ...?,
                'rating' => rand(1, 5),
                'parent_id' => $faker->optional()->randomDigitNotNull(),
                'comment' => $faker->sentence(),
                'images' => null,
                'publish' => $faker->boolean(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
