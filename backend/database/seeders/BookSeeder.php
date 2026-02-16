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
                'description' => 'Роман-эпопея',
                'genre_id' => 1,
                'publisher_id' => 1,
                'format_id' => 1,
                'file_path' => 'books1.pdf',
                'file_size_bytes' => 111,
            ],
            [
                'book_id' => 2,
                'book_title' => 'Книга 2',
                'description' => 'Роман-эпопея',
                'genre_id' => 2,
                'publisher_id' => 2,
                'format_id' => 1,
                'file_path' => 'books2.pdf',
                'file_size_bytes' => 112,
            ],
        ]);
    }
}
