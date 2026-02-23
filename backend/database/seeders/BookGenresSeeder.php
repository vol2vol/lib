<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class BookGenresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
        {
            DB::table('book_genres')->insert([
                ['book_id' => 1, 'genre_id' => 1],
                ['book_id' => 2, 'genre_id' => 1],
                ['book_id' => 2, 'genre_id' => 2],
        ]);
    }
}
