<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Author;

class AuthorSeeder extends Seeder
{
    public function run(): void
    {
        Author::firstOrCreate([
            'last_name' => 'Толстой',
            'first_name' => 'Лев',
            'middle_name' => 'Николаевич'
        ]);

        Author::firstOrCreate([
            'last_name' => 'Достоевский',
            'first_name' => 'Фёдор',
            'middle_name' => 'Михайлович'
        ]);

        Author::factory()->count(28)->create();
    }
}
