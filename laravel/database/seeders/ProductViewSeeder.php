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
        // Lấy tất cả các ID của product_variant và user để tránh việc gọi DB trong mỗi vòng lặp
        $productVariantIds = ProductVariant::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();

        $data = [];
        $totalRecords = 1000000; // Giảm số lượng bản ghi để dễ kiểm tra

        for ($i = 1; $i <= $totalRecords; $i++) {
            $data[] = [
                'product_variant_id' => $productVariantIds[array_rand($productVariantIds)],
                'user_id' => $userIds[array_rand($userIds)],
                'viewed_at' => Carbon::now()->subMinutes(rand(1, 10000)),  // Thời gian xem ngẫu nhiên trong quá khứ
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            // Chèn dữ liệu vào DB sau mỗi 500 bản ghi
            if ($i % 500 === 0) {
                DB::table('product_views')->insert($data);
                $data = []; // Reset mảng dữ liệu sau mỗi lần insert
            }
        }

        // Chèn nốt các bản ghi còn lại nếu có
        if (!empty($data)) {
            DB::table('product_views')->insert($data);
        }
    }
}
