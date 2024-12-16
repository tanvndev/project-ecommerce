<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Carbon\Carbon;

class WishlistsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 1000; $i++) {
            $randomDate = Carbon::createFromTimestamp(rand(
                Carbon::create(2023, 1, 1)->timestamp,
                Carbon::create(2024, 12, 31)->timestamp
            ));

            DB::table('wishlists')->insert([
                'user_id' => rand(22, 200),
                'product_variant_id' => rand(215, 400),
                'created_at' => $randomDate,
                'updated_at' => now(),
            ]);
        }
    }
}
