<?php

namespace Tests\Feature\Api;

use App\Models\Publisher;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublisherControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_publishers()
    {
        Publisher::factory()->count(5)->create();

        $response = $this->getJson('/api/publishers');

        $response->assertStatus(200);
        $response->assertJsonCount(5);
        $response->assertJsonStructure([
            '*' => [
                'publisher_id',
                'publisher_name',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_can_get_single_publisher()
    {
        $publisher = Publisher::factory()->create([
            'publisher_name' => 'АСТ',
        ]);

        $response = $this->getJson("/api/publishers/{$publisher->publisher_id}");

        $response->assertStatus(200);
        $response->assertJson([
            'publisher_id' => $publisher->publisher_id,
            'publisher_name' => 'АСТ',
        ]);
    }

    // public function test_get_publisher_includes_related_books()
    // {
    //     $publisher = Publisher::factory()->create();
    //     $books = Book::factory()->count(3)->create([
    //         'publisher_id' => $publisher->publisher_id,
    //     ]);

    //     $response = $this->getJson("/api/publishers/{$publisher->publisher_id}");

    //     $response->assertStatus(200);
    //     $response->assertJsonCount(3, 'books');
    //     $response->assertJsonStructure([
    //         'publisher_id',
    //         'publisher_name',
    //         'books' => [
    //             '*' => [
    //                 'book_id',
    //                 'book_title',
    //                 'description',
    //                 'published_year',
    //                 'publisher_id',
    //                 'format_id',
    //                 'file_path',
    //                 'file_size_bytes',
    //                 'created_at',
    //                 'updated_at'
    //             ],
    //         ],
    //     ]);
    // }

    public function test_returns_404_for_nonexistent_publisher()
    {
        $response = $this->getJson('/api/publishers/999999');

        $response->assertStatus(404);
    }

    public function test_empty_publishers_list_returns_empty_array()
    {
        $response = $this->getJson('/api/publishers');

        $response->assertStatus(200);
        $response->assertJson([]);
    }

    public function test_publisher_with_no_books_returns_empty_books_array()
    {
        $publisher = Publisher::factory()->create();

        $response = $this->getJson("/api/publishers/{$publisher->publisher_id}");

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'books');
    }
}