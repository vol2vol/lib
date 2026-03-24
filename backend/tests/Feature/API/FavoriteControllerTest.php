<?php

namespace Tests\Feature\API;

use App\Models\Book;
use App\Models\BookFile;
use App\Models\Genre;
use App\Models\Author;
use App\Models\Publisher;
use App\Models\Format;
use App\Models\User;
use Database\Seeders\FormatSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FavoriteControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(FormatSeeder::class);
    }

    // ==================== ТЕСТЫ МЕТОДА index() ====================

    public function test_index_requires_authentication()
    {
        $response = $this->getJson('/api/favorites');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Требуется авторизация'
            ]);
    }

    public function test_index_returns_empty_favorites_for_user_with_no_favorites()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/favorites');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'data' => [],
                    'total' => 0,
                    'per_page' => 15,
                    'current_page' => 1,
                    'last_page' => 1
                ]
            ]);
    }

    public function test_index_returns_paginated_favorites_list()
    {
        $user = User::factory()->create();
        $books = Book::factory()->count(25)->create();

        foreach ($books as $book) {
            $user->favoriteBooks()->attach($book);
        }

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/favorites?per_page=10');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'per_page' => 10,
                    'current_page' => 1,
                    'total' => 25,
                    'last_page' => 3
                ]
            ])
            ->assertJsonCount(10, 'data.data');
    }

    public function test_index_favorite_book_item_has_correct_structure()
    {
        $user = User::factory()->create();
        $publisher = Publisher::factory()->create(['publisher_name' => 'Эксмо']);
        $genre = Genre::factory()->create(['genre_name' => 'Фантастика']);
        $author = Author::factory()->create([
            'last_name' => 'Лукьяненко',
            'first_name' => 'Сергей',
            'middle_name' => 'Васильевич'
        ]);
        $format = Format::factory()->create(['format_name' => 'EPUB']);

        $book = Book::factory()->create([
            'book_title' => 'Ночной дозор',
            'published_year' => 1998,
            'cover_path' => 'covers/night_watch.jpg',
            'publisher_id' => $publisher->publisher_id
        ]);

        $book->genres()->attach($genre);
        $book->authors()->attach($author);
        BookFile::factory()->create([
            'book_id' => $book->book_id,
            'format_id' => $format->format_id,
            'file_size_bytes' => 2048576
        ]);

        $user->favoriteBooks()->attach($book);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/favorites');

        $response->assertStatus(200);
        $favoriteData = $response->json('data.data')[0];

        $this->assertEquals($book->book_id, $favoriteData['book_id']);
        $this->assertEquals('Ночной дозор', $favoriteData['book_title']);
        $this->assertEquals(1998, $favoriteData['published_year']);
        $this->assertEquals('/api/covers/night_watch.jpg', $favoriteData['cover_url']);
        $this->assertCount(1, $favoriteData['genres']);
        $this->assertEquals('Фантастика', $favoriteData['genres'][0]['genre_name']);
        $this->assertCount(1, $favoriteData['authors']);
        $this->assertEquals('Лукьяненко', $favoriteData['authors'][0]['last_name']);
        $this->assertEquals('Эксмо', $favoriteData['publisher']);
        $this->assertEquals(1, $favoriteData['files_count']);
    }

    public function test_index_handles_book_without_cover_path()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['cover_path' => null]);
        $user->favoriteBooks()->attach($book);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/favorites');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'cover_url' => null
            ]);
    }

    public function test_index_handles_book_without_genres()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $user->favoriteBooks()->attach($book);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/favorites');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'genres' => []
            ]);
    }

    public function test_index_handles_book_without_authors()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $user->favoriteBooks()->attach($book);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/favorites');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'authors' => []
            ]);
    }

    public function test_index_handles_book_without_files()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $user->favoriteBooks()->attach($book);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/favorites');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'files_count' => 0
            ]);
    }

    public function test_index_preserves_cyrillic_characters()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'book_title' => 'Ёжик в тумане',
            'description' => 'Сказка о дружбе'
        ]);
        $user->favoriteBooks()->attach($book);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/favorites');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'book_title' => 'Ёжик в тумане'
            ]);
    }

    public function test_index_default_per_page_value()
    {
        $user = User::factory()->create();
        Book::factory()->count(20)->create()->each(fn($b) => $user->favoriteBooks()->attach($b));

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/favorites');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'per_page' => 15
                ]
            ]);
    }

    // ==================== ТЕСТЫ МЕТОДА store() ====================

    public function test_store_requires_authentication()
    {
        $book = Book::factory()->create();

        $response = $this->postJson("/api/favorites/{$book->book_id}");

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Требуется авторизация'
            ]);
    }

    public function test_store_returns_404_for_nonexistent_book()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/favorites/999999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Книга не найдена'
            ]);
    }

    public function test_store_returns_400_if_book_already_in_favorites()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $user->favoriteBooks()->attach($book);

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/favorites/{$book->book_id}");

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Книга уже в избранном'
            ]);
    }

    public function test_store_successfully_adds_book_to_favorites()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/favorites/{$book->book_id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Книга добавлена в избранное'
            ]);

        $this->assertDatabaseHas('favorite_books', [
            'user_id' => $user->user_id,
            'book_id' => $book->book_id
        ]);
    }

    public function test_store_with_numeric_string_book_id()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/favorites/{$book->book_id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertTrue($user->favoriteBooks->contains($book));
    }

    // ==================== ТЕСТЫ МЕТОДА remove() ====================

    public function test_remove_requires_authentication()
    {
        $book = Book::factory()->create();

        $response = $this->deleteJson("/api/favorites/{$book->book_id}");

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Требуется авторизация'
            ]);
    }

    public function test_remove_returns_404_for_nonexistent_book()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/favorites/999999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Книга не найдена'
            ]);
    }

    public function test_remove_returns_400_if_book_not_in_favorites()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/favorites/{$book->book_id}");

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Книги нет в избранном'
            ]);
    }

    public function test_remove_successfully_removes_book_from_favorites()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $user->favoriteBooks()->attach($book);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/favorites/{$book->book_id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Книга удалена из избранного'
            ]);

        $this->assertDatabaseMissing('favorite_books', [
            'user_id' => $user->user_id,
            'book_id' => $book->book_id
        ]);
    }

    public function test_remove_with_numeric_string_book_id()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $user->favoriteBooks()->attach($book);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/favorites/{$book->book_id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertFalse($user->favoriteBooks->contains($book));
    }

    // ==================== ИНТЕГРАЦИОННЫЕ ТЕСТЫ ====================

    public function test_full_favorite_lifecycle()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'book_title' => 'Тестовая книга',
            'published_year' => 2024
        ]);

        // 1. Добавляем в избранное
        $response = $this->actingAs($user, 'sanctum')->postJson("/api/favorites/{$book->book_id}");
        $response->assertStatus(200)->assertJson(['success' => true]);

        // 2. Проверяем, что книга в списке избранного
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/favorites');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonFragment(['book_title' => 'Тестовая книга']);

        // 3. Пытаемся добавить ту же книгу снова (должно вернуть 400)
        $response = $this->actingAs($user, 'sanctum')->postJson("/api/favorites/{$book->book_id}");
        $response->assertStatus(400)
            ->assertJson(['message' => 'Книга уже в избранном']);

        // 4. Удаляем из избранного
        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/favorites/{$book->book_id}");
        $response->assertStatus(200)->assertJson(['success' => true]);

        // 5. Проверяем, что книга больше не в списке
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/favorites');
        $response->assertStatus(200)
            ->assertJsonCount(0, 'data.data');

        // 6. Пытаемся удалить несуществующую запись (должно вернуть 400)
        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/favorites/{$book->book_id}");
        $response->assertStatus(400)
            ->assertJson(['message' => 'Книги нет в избранном']);
    }

    public function test_multiple_users_have_separate_favorites()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $book1 = Book::factory()->create(['book_title' => 'Книга 1']);
        $book2 = Book::factory()->create(['book_title' => 'Книга 2']);

        $user1->favoriteBooks()->attach($book1);
        $user2->favoriteBooks()->attach($book2);

        $response1 = $this->actingAs($user1, 'sanctum')->getJson('/api/favorites');
        $response2 = $this->actingAs($user2, 'sanctum')->getJson('/api/favorites');

        $response1->assertStatus(200)
            ->assertJsonFragment(['book_title' => 'Книга 1'])
            ->assertJsonMissing(['book_title' => 'Книга 2']);

        $response2->assertStatus(200)
            ->assertJsonFragment(['book_title' => 'Книга 2'])
            ->assertJsonMissing(['book_title' => 'Книга 1']);
    }

    public function test_favorites_with_complex_book_data()
    {
        $user = User::factory()->create();
        $publisher = Publisher::factory()->create(['publisher_name' => 'АСТ']);
        $genre1 = Genre::factory()->create(['genre_name' => 'Фантастика']);
        $genre2 = Genre::factory()->create(['genre_name' => 'Приключения']);
        $author1 = Author::factory()->create(['last_name' => 'Толкин', 'first_name' => 'Джон', 'middle_name' => 'Роналд Руэл']);
        $author2 = Author::factory()->create(['last_name' => 'Льюис', 'first_name' => 'Клайв', 'middle_name' => 'Стейплз']);
        $format1 = Format::factory()->create(['format_name' => 'RTF']);
        $format2 = Format::factory()->create(['format_name' => 'DOCX']);

        $book = Book::factory()->create([
            'book_title' => 'Властелин колец',
            'published_year' => 1954,
            'cover_path' => 'covers/lotr.jpg',
            'publisher_id' => $publisher->publisher_id
        ]);

        $book->genres()->attach([$genre1, $genre2]);
        $book->authors()->attach([$author1, $author2]);
        BookFile::factory()->create(['book_id' => $book->book_id, 'format_id' => $format1->format_id, 'file_size_bytes' => 5242880]);
        BookFile::factory()->create(['book_id' => $book->book_id, 'format_id' => $format2->format_id, 'file_size_bytes' => 3145728]);

        $user->favoriteBooks()->attach($book);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/favorites');

        $response->assertStatus(200);
        $favorite = $response->json('data.data')[0];

        $this->assertEquals('Властелин колец', $favorite['book_title']);
        $this->assertEquals(1954, $favorite['published_year']);
        $this->assertEquals('/api/covers/lotr.jpg', $favorite['cover_url']);
        $this->assertEquals('АСТ', $favorite['publisher']);
        $this->assertCount(2, $favorite['genres']);
        $this->assertCount(2, $favorite['authors']);
        $this->assertEquals(2, $favorite['files_count']);

        $genreNames = collect($favorite['genres'])->pluck('genre_name')->toArray();
        $this->assertContains('Фантастика', $genreNames);
        $this->assertContains('Приключения', $genreNames);

        $authorLastNames = collect($favorite['authors'])->pluck('last_name')->toArray();
        $this->assertContains('Толкин', $authorLastNames);
        $this->assertContains('Льюис', $authorLastNames);
    }

    public function test_unicode_encoding_in_responses()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'book_title' => 'Привет, мир! 📚',
        ]);
        $user->favoriteBooks()->attach($book);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/favorites');

        $response->assertStatus(200);
        $this->assertStringContainsString('Привет, мир! 📚', $response->getContent());
    }

    public function test_pagination_navigation_links()
    {
        $user = User::factory()->create();
        Book::factory()->count(30)->create()->each(fn($b) => $user->favoriteBooks()->attach($b));

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/favorites?per_page=10&page=2');

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertEquals(2, $data['current_page']);
        $this->assertEquals(3, $data['last_page']);
        $this->assertNotNull($data['prev_page_url']);
        $this->assertNotNull($data['next_page_url']);
        $response->assertJsonCount(10, 'data.data');
    }

    public function test_store_with_zero_book_id()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/favorites/0');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Книга не найдена']);
    }

    public function test_remove_with_zero_book_id()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->deleteJson('/api/favorites/0');

        $response->assertStatus(404)
            ->assertJson(['message' => 'Книга не найдена']);
    }
}