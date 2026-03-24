<?php

namespace Tests\Unit\Seeder;

use Database\Seeders\GenreSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_genre_seeder_creates_genres()
    {
        $this->seed(GenreSeeder::class);

        $this->assertDatabaseCount('genres', 48);
    }

    public function test_genre_seeder_does_not_duplicate_genres()
    {
        $this->seed(GenreSeeder::class);
        $this->seed(GenreSeeder::class);

        $this->assertDatabaseCount('genres', 48);
    }
}