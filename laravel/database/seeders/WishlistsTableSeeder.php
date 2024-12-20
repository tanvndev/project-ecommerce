<?php

namespace Database\Seeders;

use App\Models\ProductVariant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WishlistsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $productVariantIds = ProductVariant::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();

        for ($i = 0; $i < 1000001; $i++) {
            $startDate = Carbon::createFromFormat('Y-m-d', '2023-01-01');
            $endDate = Carbon::createFromFormat('Y-m-d', '2024-12-31');

            $randomDate = $startDate->copy()->addDays(rand(0, $endDate->diffInDays($startDate)));

            DB::table('wishlists')->insert([
                'user_id'            => $userIds[array_rand($userIds)],
                'product_variant_id' => $productVariantIds[array_rand($productVariantIds)],
                'created_at'         => $randomDate,
                'updated_at'         => $randomDate,
            ]);
        }
    }
}
