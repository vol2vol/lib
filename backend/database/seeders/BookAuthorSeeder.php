<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class BookAuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('book_authors')->insert([
            [
                'book_id' => 1,
                'author_id' => 1,
            ],
            [
                'book_id' => 2,
                'author_id' => 1,
            ],
            [
                'book_id' => 2,
                'author_id' => 2,
            ],
        ]);
    }
}
