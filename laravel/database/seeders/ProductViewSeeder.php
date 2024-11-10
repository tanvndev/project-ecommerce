<?php

namespace Database\Seeders;

use App\Models\ProductVariant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductViewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


    public function run(): void
    {
        $data = [];
        $totalRecords = 1000000000;
        for ($i = 1; $i < $totalRecords; $i++) {
            $data[] = [
                'product_variant_id' => ProductVariant::all()->random()->id,
                'user_id'            => rand(1, 10) === 1 ? null : User::all()->random()->id,  // Giả định bạn có các user_id từ 1 đến 50
                'viewed_at'          => Carbon::now()->subMinutes(rand(1, 10000)),  // Thời gian xem ngẫu nhiên trong quá khứ
                'created_at'         => Carbon::now(),
                'updated_at'         => Carbon::now(),
            ];

            // Chèn dữ liệu vào DB sau mỗi 500 bản ghi để tránh tràn bộ nhớ
            if ($i % 500 === 0) {
                DB::table('product_views')->insert($data);
                $data = [];
            }
        }
    }
}
