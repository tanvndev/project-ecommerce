<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProhibitedWord;

class ProhibitedWordsSeeder extends Seeder
{
    public function run()
    {
        $filePath = storage_path('prohibited_words.txt');

        if (file_exists($filePath)) {
            $prohibitedWords = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($prohibitedWords as $word) {
                ProhibitedWord::firstOrCreate([
                    'keyword' => $word,
                ]);
            }

            echo "Seeder data inserted successfully.\n";
        } else {
            echo "File not found.\n";
        }
    }
}
