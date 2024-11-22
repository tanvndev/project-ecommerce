<?php

namespace Database\Seeders;

use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartActionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy tất cả các ID của product_variant và user để tránh việc gọi DB trong mỗi vòng lặp
        $productVariantIds = ProductVariant::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();

        $batchSize = 1000;
        $totalRecords = 1000000;
        $data = [];

        for ($i = 1; $i <= $totalRecords; $i++) {
            $data[] = [
                'product_variant_id' => $productVariantIds[array_rand($productVariantIds)],
                'user_id'            => $userIds[array_rand($userIds)],
                'action'             => fake()->randomElement(['added', 'removed']),
                'created_at'         => now(),
                'updated_at'         => now(),
            ];

            if ($i % $batchSize === 0 || $i === $totalRecords) {
                DB::table('cart_actions')->insert($data);
                $data = [];
            }
        }
    }
}
