<?php

namespace Tests\Feature\Api;

use App\Models\Format;
use App\Models\BookFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormatControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_formats()
    {
        Format::factory()->count(5)->create();

        $response = $this->getJson('/api/formats');

        $response->assertStatus(200);
        $response->assertJsonCount(5);
        $response->assertJsonStructure([
            '*' => [
                'format_id',
                'format_name',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_can_get_single_format()
    {
        $format = Format::factory()->create([
            'format_name' => 'PDF',
        ]);

        $response = $this->getJson("/api/formats/{$format->format_id}");

        $response->assertStatus(200);
        $response->assertJson([
            'format_id' => $format->format_id,
            'format_name' => 'PDF',
            'books' => [],
        ]);
    }

    public function test_get_format_includes_related_books()
    {
        $format = Format::factory()->create();
        $book_files = BookFile::factory()->count(3)->create(['format_id' => $format->format_id]);

        $response = $this->getJson("/api/formats/{$format->format_id}");

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'books');
        $response->assertJsonStructure([
            'format_id',
            'format_name',
            'created_at',
            'updated_at',
            'books' => [
                '*' => [
                    'book_id',
                    'book_title',
                    'description',
                    'published_year',
                    'publisher_id',
                    'created_at',
                    'updated_at',
                    'cover_path',
                    'pivot' => [
                        'format_id',
                        'book_id'
                    ]
                ],
            ],
        ]);
    }

    public function test_returns_404_for_nonexistent_format()
    {
        $response = $this->getJson('/api/formats/999999');

        $response->assertStatus(404);
    }

    public function test_empty_formats_list_returns_empty_array()
    {
        $response = $this->getJson('/api/formats');

        $response->assertStatus(200);
        $response->assertJson([]);
    }

    public function test_format_with_no_book_returns_empty_files_array()
    {
        $format = Format::factory()->create();

        $response = $this->getJson("/api/formats/{$format->format_id}");

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'books');
    }
}