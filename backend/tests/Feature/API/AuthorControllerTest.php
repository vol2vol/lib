<?php

namespace Tests\Feature\Api;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_all_authors()
    {
        Author::factory()->count(5)->create();

        $response = $this->getJson('/api/authors');

        $response->assertStatus(200);
        $response->assertJsonCount(5);
        $response->assertJsonStructure([
            '*' => [
                'author_id',
                'last_name',
                'first_name',
                'middle_name',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_can_get_single_author()
    {
        $author = Author::factory()->create([
            'last_name' => 'Достоевский',
            'first_name' => 'Фёдор',
            'middle_name' => 'Михайлович',
        ]);

        $response = $this->getJson("/api/authors/{$author->author_id}");

        $response->assertStatus(200);
        $response->assertJson([
            'author_id' => $author->author_id,
            'last_name' => 'Достоевский',
            'first_name' => 'Фёдор',
            'middle_name' => 'Михайлович',
        ]);
    }

    public function test_get_genre_includes_related_books()
    {
        $author = Author::factory()->create();
        $books = Book::factory()->count(3)->withAuthor($author)->create();

        $response = $this->getJson("/api/authors/{$author->author_id}");

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'books');
        $response->assertJsonStructure([
            'author_id',
            'last_name',
            'first_name',
            'middle_name',
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
                        'author_id',
                        'book_id'
                    ]
                ],
            ],
        ]);
    }

    public function test_returns_404_for_nonexistent_author()
    {
        $response = $this->getJson('/api/authors/999999');

        $response->assertStatus(404);
    }

    public function test_empty_authors_list_returns_empty_array()
    {
        $response = $this->getJson('/api/authors');

        $response->assertStatus(200);
        $response->assertJson([]);
    }

    public function test_author_with_no_books_returns_empty_books_array()
    {
        $author = Author::factory()->create();

        $response = $this->getJson("/api/authors/{$author->author_id}");

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'books');
    }
}