<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\User;
use Carbon\Carbon;
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

        $productIds = Product::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();
        $orderIds = User::pluck('id')->toArray();

        try {
            for ($i = 0; $i < 1000001; $i++) { // Tạo 100 bản ghi
                $startDate = Carbon::createFromFormat('Y-m-d', '2023-01-01');
                $endDate = Carbon::createFromFormat('Y-m-d', '2024-12-31');

                $randomDate = $startDate->copy()->addDays(rand(0, $endDate->diffInDays($startDate)));

                try {
                    DB::table('product_reviews')->insert([
                        'product_id' => $productIds[array_rand($productIds)],
                        'user_id' => $userIds[array_rand($userIds)],
                        'order_id' => $orderIds[array_rand($orderIds)],
                        'rating' => rand(1, 5),
                        'parent_id' => null,
                        'comment' => $faker->sentence(),
                        'images' => null,
                        'publish' => $faker->boolean(),
                        'created_at' => $randomDate,
                        'updated_at' => $randomDate,
                    ]);
                } catch (\Throwable $th) {
                }
            }
        } catch (\Throwable $th) {
        }
    }
}
