<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('books')->insert([
            [
                'book_id' => 1,
                'book_title' => 'Война и мир',
                'published_year' => '1834',
                'description' => 'Роман-эпопея',
                'publisher_id' => 1,
                'format_id' => 1,
                'file_path' => 'books1.pdf',
                'file_size_bytes' => 111,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'book_id' => 2,
                'book_title' => 'Книга 2',
                'published_year' => '1834',
                'description' => 'Роман-эпопея',
                'publisher_id' => 2,
                'format_id' => 1,
                'file_path' => 'books2.pdf',
                'file_size_bytes' => 112,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
