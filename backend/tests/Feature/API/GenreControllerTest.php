<?php

namespace Tests\Feature\Api;

use App\Models\Genre;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_genres()
    {
        Genre::factory()->count(5)->create();

        $response = $this->getJson('/api/genres');

        $response->assertStatus(200);
        $response->assertJsonCount(5);
        $response->assertJsonStructure([
            '*' => [
                'genre_id',
                'genre_name',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_can_get_single_genre()
    {
        $genre = Genre::factory()->create([
            'genre_name' => 'Фантастика',
        ]);

        $response = $this->getJson("/api/genres/{$genre->genre_id}");

        $response->assertStatus(200);
        $response->assertJson([
            'genre_id' => $genre->genre_id,
            'genre_name' => 'Фантастика',
        ]);
    }

    public function test_get_genre_includes_related_books()
    {
        $genre = Genre::factory()->create();
        $books = Book::factory()->count(3)->withGenre($genre)->create();

        $response = $this->getJson("/api/genres/{$genre->genre_id}");

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'books');
        $response->assertJsonStructure([
            'genre_id',
            'genre_name',
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
                        'genre_id',
                        'book_id'
                    ]
                ],
            ],
        ]);
    }

    public function test_returns_404_for_nonexistent_genre()
    {
        $response = $this->getJson('/api/genres/999999');

        $response->assertStatus(404);
    }

    public function test_empty_genres_list_returns_empty_array()
    {
        $response = $this->getJson('/api/genres');

        $response->assertStatus(200);
        $response->assertJson([]);
    }

    public function test_genre_with_no_books_returns_empty_books_array()
    {
        $genre = Genre::factory()->create();

        $response = $this->getJson("/api/genres/{$genre->genre_id}");

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'books');
    }
}