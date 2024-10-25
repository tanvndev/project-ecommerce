<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class UpdateOrderItemSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $productVariants = ProductVariant::all();

        foreach ($productVariants as $productVariant) {
            OrderItem::where('product_variant_id', $productVariant->id)
                ->update([
                    'cost_price' => $productVariant->cost_price, 
                    'updated_at' => now(),
                ]);
        }
    }
}
