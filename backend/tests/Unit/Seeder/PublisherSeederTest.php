<?php

namespace Tests\Unit\Seeder;

use Database\Seeders\PublisherSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublisherSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_publisher_seeder_creates_publishers()
    {
        $this->seed(PublisherSeeder::class);

        $this->assertDatabaseCount('publishers', 25);
    }

    public function test_pubclisher_seeder_does_not_duplicate_publishers()
    {
        $this->seed(PublisherSeeder::class);
        $this->seed(PublisherSeeder::class);

        $this->assertDatabaseCount('publishers', 25);
    }
}