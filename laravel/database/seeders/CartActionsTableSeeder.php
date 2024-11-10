<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CartActionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $batchSize = 5000;
        $totalRecords = 1000000;
        $data = [];

        for ($i = 1; $i <= $totalRecords; $i++) {
            $data[] = [
                'product_variant_id' => fake()->numberBetween(212, 417),
                'user_id' => fake()->numberBetween(22, 222),
                'action' => fake()->randomElement(['added', 'removed']),
                'created_at' => now(),
                'updated_at' => now(),
            ];


            if ($i % $batchSize === 0 || $i === $totalRecords) {
                DB::table('cart_actions')->insert($data);
                $data = [];
            }
        }
    }
}
