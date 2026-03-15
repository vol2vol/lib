<?php

namespace Tests\Unit\Seeder;

use Database\Seeders\FormatSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormatSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_format_seeder_creates_pdf_txt_and_fb2_formats()
    {
        $this->seed(FormatSeeder::class);

        $this->assertDatabaseCount('formats', 3);
        $this->assertDatabaseHas('formats', ['format_name' => 'PDF']);
        $this->assertDatabaseHas('formats', ['format_name' => 'TXT']);
        $this->assertDatabaseHas('formats', ['format_name' => 'FB2']);
    }

    public function test_format_seeder_does_not_duplicate_formats()
    {
        $this->seed(FormatSeeder::class);
        $this->seed(FormatSeeder::class);

        $this->assertDatabaseCount('formats', 3);
    }
}