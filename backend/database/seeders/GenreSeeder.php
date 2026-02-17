<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('genres')->insert([
            ['genre_id' => 1, 'genre_name' => 'Роман'],
            ['genre_id' => 2, 'genre_name' => 'Фантастика'],
            ['genre_id' => 3, 'genre_name' => 'Детектив'],
        ]);
    }
}
