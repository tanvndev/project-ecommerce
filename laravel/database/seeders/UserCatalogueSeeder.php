<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserCatalogueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user_catalogues')->insert([
            ['id' => 1, 'name' => 'Quản trị viên', 'code' => 'admin', 'description' => 'Người có quyền hành cao nhất', 'publish' => 1, 'deleted_at' => null, 'created_at' => '2024-07-20 05:47:27', 'updated_at' => '2024-07-20 05:47:27'],
            ['id' => 2, 'name' => 'Khách hàng', 'code' => 'customer', 'description' => 'Không có quyền truy cập vào trang quản trị', 'publish' => 1, 'deleted_at' => null, 'created_at' => '2024-07-20 05:48:34', 'updated_at' => '2024-08-04 08:36:22'],
            ['id' => 3, 'name' => 'Nhân viên', 'code' => 'staff', 'description' => 'Có quyền truy cập vào quản trị nhưng dùng 1 vài chức năng chỉ định', 'publish' => 1, 'deleted_at' => null, 'created_at' => '2024-07-20 05:48:34', 'updated_at' => '2024-08-04 08:36:22'],
        ]);
    }
}
