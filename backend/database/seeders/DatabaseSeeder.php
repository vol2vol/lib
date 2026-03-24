<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            FormatSeeder::class,
            GenreSeeder::class,

            AuthorSeeder::class,
            PublisherSeeder::class,
            UserSeeder::class,
            BookSeeder::class,

            FavoriteBookSeeder::class,
        ]);
    }
}
