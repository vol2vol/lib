<?php

namespace Tests\Unit\Seeder;

use Database\Seeders\FavoriteBookSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\BookSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\PublisherSeeder;
use Database\Seeders\AuthorSeeder;
use Database\Seeders\GenreSeeder;
use Database\Seeders\FormatSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteBookSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
        $this->seed(PublisherSeeder::class);
        $this->seed(AuthorSeeder::class);
        $this->seed(GenreSeeder::class);
        $this->seed(FormatSeeder::class);
        $this->seed(BookSeeder::class);
    }

    public function test_favorite_book_seeder_creates_favorite_books()
    {
        $this->seed(FavoriteBookSeeder::class);

        $this->assertDatabaseCount('favorite_books', 60);
    }

    public function test_favorite_book_seeder_does_not_duplicate_favorite_books()
    {
        $this->seed(FavoriteBookSeeder::class);
        $this->seed(FavoriteBookSeeder::class);

        $this->assertDatabaseCount('favorite_books', 60);
    }
}