<?php

namespace Tests\Unit\Seeder;

use Database\Seeders\AuthorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_seeder_creates_authors()
    {
        $this->seed(AuthorSeeder::class);

        $this->assertDatabaseCount('authors', 32);
    }

    public function test_author_seeder_does_not_duplicate_authors()
    {
        $this->seed(AuthorSeeder::class);
        $this->seed(AuthorSeeder::class);

        $this->assertDatabaseCount('authors', 32);
    }
}