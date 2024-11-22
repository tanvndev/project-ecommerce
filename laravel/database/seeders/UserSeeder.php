<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $pass = bcrypt('password');
        // $now = now();
        // $batchSize = 2000;

        // for ($i = 1; $i <= 2000001; $i++) {
        //     $data[] = [
        //         'fullname' => 'user'.$i,
        //         'email' => 'user'.$i.'@example.com',
        //         'password' => $pass,
        //         'email_verified_at' => $now,
        //         'created_at' => $now,
        //         'updated_at' => $now,
        //     ];

        //     if ($i % $batchSize === 0) {
        //         $this->insertBatch($data, $i);
        //         $data = [];
        //     }
        // }

        // if (! empty($data)) {
        //     $this->insertBatch($data, $i - 1);
        // }

        // DB::table('users')->insert([
        //     [
        //         'id'                => 1,
        //         'fullname'          => 'Tanadmin',
        //         'email'             => 'admin@gmail.com',
        //         'user_catalogue_id' => 1,
        //         'google_id'         => null,
        //         'password'          => '$2y$12$J4y0JFPmSIEEQ..2sc6VMe4qFLR3hBTeUB6nT1soY2EfHzl.lCnAC',
        //         'phone'             => '0123456789',
        //         'birthday'          => null,
        //         'image'             => 'http://127.0.0.1:8000/images/2w024/07/registerpng_669bc58c82ee7.webp',
        //         'description'       => null,
        //         'user_agent'        => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
        //         'publish'           => 1,
        //         'ip'                => '127.0.0.1',
        //         'email_verified_at' => '2024-07-21 10:56:49',
        //         'deleted_at'        => null,
        //         'created_at'        => '2024-07-21 10:56:49',
        //         'updated_at'        => '2024-07-21 11:00:02',
        //     ],
        // ]);

        $this->fakeDataUser();
    }

    private function insertBatch(array $data, int $count): void
    {
        DB::table('users')->insert($data);
        echo "Inserted {$count} rows." . PHP_EOL;
    }

    private function fakeDataUser()
    {
        $now = Carbon::now();
        $users = [];

        for ($i = 0; $i < 200; $i++) {
            $users[] = [
                'fullname'          => "Customer{$i}",
                'email'             => "customer{$i}@customer.com",
                'user_catalogue_id' => 2,
                'google_id'         => null,
                'password'          => '$2y$12$J4y0JFPmSIEEQ..2sc6VMe4qFLR3hBTeUB6nT1soY2EfHzl.lCnAC',
                'phone'             => '0332225' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'birthday'          => null,
                'image'             => 'http://127.0.0.1:8000/images/2024/10/downloadjpeg_6701ec630f437.webp',
                'description'       => null,
                'user_agent'        => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
                'publish'           => 1,
                'ip'                => '127.0.0.1',
                'email_verified_at' => $now,
                'deleted_at'        => null,
                'created_at'        => $now,
                'updated_at'        => $now,
            ];
        }

        DB::table('users')->insert($users);
    }
}
