<?php

namespace Tests\Unit\Seeder;

use Database\Seeders\BookSeeder;
use Database\Seeders\PublisherSeeder;
use Database\Seeders\AuthorSeeder;
use Database\Seeders\GenreSeeder;
use Database\Seeders\FormatSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PublisherSeeder::class);
        $this->seed(AuthorSeeder::class);
        $this->seed(GenreSeeder::class);
        $this->seed(FormatSeeder::class);
    }

    public function test_book_seeder_creates_books()
    {
        $this->seed(BookSeeder::class);

        $this->assertDatabaseCount('books', 30);
    }

    public function test_book_seeder_does_not_duplicate_books()
    {
        $this->seed(BookSeeder::class);
        $this->seed(BookSeeder::class);

        $this->assertDatabaseCount('books', 30);
    }
}