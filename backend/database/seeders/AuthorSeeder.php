<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('authors')->insert([
            [
                'author_id' => 1,
                'last_name' => 'Толстой',
                'first_name' => 'Лев',
                'middle_name' => 'Николаевич',
            ],
            [
                'author_id' => 2,
                'last_name' => 'Достоевский',
                'first_name' => 'Фёдор',
                'middle_name' => 'Михайлович',
            ],
        ]);
    }
}
