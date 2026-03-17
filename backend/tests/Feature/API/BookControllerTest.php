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
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(FormatSeeder::class);
    }

    /**
     * Тесты метода index() — базовые
     */

    public function test_index_returns_successful_response_with_empty_database()
    {
        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'total'
            ])
            ->assertJson([
                'success' => true,
                'data' => [],
                'total' => 0
            ]);
    }

    public function test_index_returns_paginated_books_list()
    {
        $publisher = Publisher::factory()->create();
        $books = Book::factory()->count(20)->create([
            'publisher_id' => $publisher->publisher_id,
            'cover_path' => 'covers/test.jpg'
        ]);

        $response = $this->getJson('/api/books?per_page=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'current_page',
                'data',
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total'
            ])
            ->assertJsonCount(10, 'data')
            ->assertJson([
                'success' => true,
                'per_page' => 10,
                'total' => 20
            ]);
    }

    public function test_index_book_item_has_correct_structure()
    {
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

        $response = $this->getJson('/api/books');

        $response->assertStatus(200);
        $bookData = $response->json('data')[0];

        $this->assertEquals($book->book_id, $bookData['book_id']);
        $this->assertEquals('Ночной дозор', $bookData['book_title']);
        $this->assertEquals(1998, $bookData['published_year']);
        $this->assertEquals('/api/covers/night_watch.jpg', $bookData['cover_url']);
        $this->assertFalse($bookData['is_favorited']);
        $this->assertCount(1, $bookData['genres']);
        $this->assertEquals('Фантастика', $bookData['genres'][0]['genre_name']);
        $this->assertCount(1, $bookData['authors']);
        $this->assertEquals('Лукьяненко', $bookData['authors'][0]['last_name']);
        $this->assertEquals('Эксмо', $bookData['publisher']['publisher_name']);
        $this->assertEquals(1, $bookData['files_count']);
    }

    /**
     * Тесты валидации параметров
     */

    public function test_index_rejects_invalid_page_parameter()
    {
        $response = $this->getJson('/api/books?page=abc');

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Ошибка валидации параметров'
            ])
            ->assertJsonValidationErrors(['page']);
    }

    public function test_index_rejects_page_less_than_one()
    {
        $response = $this->getJson('/api/books?page=0');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['page']);
    }

    public function test_index_rejects_invalid_per_page_parameter()
    {
        $response = $this->getJson('/api/books?per_page=200');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['per_page']);
    }

    public function test_index_rejects_search_too_short()
    {
        $response = $this->getJson('/api/books?search=a');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['search']);
    }

    public function test_index_rejects_search_too_long()
    {
        $response = $this->getJson('/api/books?search=' . str_repeat('a', 101));

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['search']);
    }

    public function test_index_rejects_nonexistent_genre_id()
    {
        $response = $this->getJson('/api/books?genre_id=999999');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['genre_id']);
    }

    public function test_index_rejects_nonexistent_author_id()
    {
        $response = $this->getJson('/api/books?author_id=999999');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['author_id']);
    }

    public function test_index_rejects_nonexistent_publisher_id()
    {
        $response = $this->getJson('/api/books?publisher_id=999999');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['publisher_id']);
    }

    public function test_index_rejects_year_out_of_range()
    {
        $response = $this->getJson('/api/books?year_from=500');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['year_from']);
    }

    public function test_index_rejects_invalid_sort_field()
    {
        $response = $this->getJson('/api/books?sort=invalid_field');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['sort']);
    }

    public function test_index_rejects_invalid_order_direction()
    {
        $response = $this->getJson('/api/books?order=invalid');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['order']);
    }

    /**
     * Тесты поиска и фильтрации
     */

    public function test_index_searches_by_book_title()
    {
        Book::factory()->create(['book_title' => 'Дом, в котором...']);
        Book::factory()->create(['book_title' => 'Анна Каренина']);
        Book::factory()->create(['book_title' => 'Дом секретов']);

        $response = $this->getJson('/api/books?search=Дом');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');

        $titles = collect($response->json('data'))->pluck('book_title');
        $this->assertContains('Дом, в котором...', $titles);
        $this->assertContains('Дом секретов', $titles);
        $this->assertNotContains('Анна Каренина', $titles);
    }

    public function test_index_searches_by_description()
    {
        Book::factory()->create([
            'book_title' => 'Книга 1',
            'description' => 'Эпическое фэнтези о драконах'
        ]);
        Book::factory()->create([
            'book_title' => 'Книга 2',
            'description' => 'Детектив в стиле нуар'
        ]);

        $response = $this->getJson('/api/books?search=драконах');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['book_title' => 'Книга 1']);
    }

    public function test_index_filters_by_genre_id()
    {
        $genre1 = Genre::factory()->create();
        $genre2 = Genre::factory()->create();

        $book1 = Book::factory()->create();
        $book2 = Book::factory()->create();
        $book3 = Book::factory()->create();

        $book1->genres()->attach($genre1);
        $book2->genres()->attach($genre2);
        $book3->genres()->attach([$genre1, $genre2]);

        $response = $this->getJson("/api/books?genre_id={$genre1->genre_id}");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');

        $ids = collect($response->json('data'))->pluck('book_id');
        $this->assertContains($book1->book_id, $ids);
        $this->assertContains($book3->book_id, $ids);
        $this->assertNotContains($book2->book_id, $ids);
    }

    public function test_index_filters_by_author_id()
    {
        $author1 = Author::factory()->create();
        $author2 = Author::factory()->create();

        $book1 = Book::factory()->create();
        $book2 = Book::factory()->create();
        $book3 = Book::factory()->create();

        $book1->authors()->attach($author1);
        $book2->authors()->attach($author2);
        $book3->authors()->attach([$author1, $author2]);

        $response = $this->getJson("/api/books?author_id={$author1->author_id}");

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');

        $ids = collect($response->json('data'))->pluck('book_id');
        $this->assertContains($book1->book_id, $ids);
        $this->assertContains($book3->book_id, $ids);
        $this->assertNotContains($book2->book_id, $ids);
    }

    public function test_index_filters_by_publisher_id()
    {
        $publisher1 = Publisher::factory()->create();
        $publisher2 = Publisher::factory()->create();

        Book::factory()->count(3)->create(['publisher_id' => $publisher1->publisher_id]);
        Book::factory()->count(2)->create(['publisher_id' => $publisher2->publisher_id]);

        $response = $this->getJson("/api/books?publisher_id={$publisher1->publisher_id}");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');

        $ids = collect($response->json('data'))->pluck('publisher.publisher_id');
        $this->assertTrue($ids->every(fn($id) => $id === $publisher1->publisher_id));
    }

    public function test_index_filters_by_year_from()
    {
        Book::factory()->create(['published_year' => 1990]);
        Book::factory()->create(['published_year' => 2000]);
        Book::factory()->create(['published_year' => 2010]);

        $response = $this->getJson('/api/books?year_from=2000');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');

        $years = collect($response->json('data'))->pluck('published_year');
        $this->assertTrue($years->every(fn($y) => $y >= 2000));
    }

    public function test_index_filters_by_year_to()
    {
        Book::factory()->create(['published_year' => 1990]);
        Book::factory()->create(['published_year' => 2000]);
        Book::factory()->create(['published_year' => 2010]);

        $response = $this->getJson('/api/books?year_to=2000');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');

        $years = collect($response->json('data'))->pluck('published_year');
        $this->assertTrue($years->every(fn($y) => $y <= 2000));
    }

    public function test_index_filters_by_year_range()
    {
        Book::factory()->create(['published_year' => 1980]);
        Book::factory()->create(['published_year' => 1995]);
        Book::factory()->create(['published_year' => 2005]);
        Book::factory()->create(['published_year' => 2020]);

        $response = $this->getJson('/api/books?year_from=1990&year_to=2010');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');

        $years = collect($response->json('data'))->pluck('published_year');
        $this->assertContains(1995, $years);
        $this->assertContains(2005, $years);
        $this->assertNotContains(1980, $years);
        $this->assertNotContains(2020, $years);
    }

    public function test_index_combines_multiple_filters()
    {
        $publisher = Publisher::factory()->create();
        $genre = Genre::factory()->create();
        $author = Author::factory()->create();

        $book1 = Book::factory()->create([
            'publisher_id' => $publisher->publisher_id,
            'published_year' => 2000
        ]);
        $book1->genres()->attach($genre);
        $book1->authors()->attach($author);

        $book2 = Book::factory()->create([
            'publisher_id' => $publisher->publisher_id,
            'published_year' => 2010
        ]);
        $book2->genres()->attach($genre);

        $book3 = Book::factory()->create([
            'publisher_id' => Publisher::factory()->create()->publisher_id,
            'published_year' => 2000
        ]);
        $book3->genres()->attach($genre);
        $book3->authors()->attach($author);

        $response = $this->getJson(sprintf(
            '/api/books?publisher_id=%d&genre_id=%d&author_id=%d&year_from=1995&year_to=2005',
            $publisher->publisher_id,
            $genre->genre_id,
            $author->author_id
        ));

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['book_id' => $book1->book_id]);
    }

    /**
     * Тесты сортировки
     */

    public function test_index_sorts_by_book_title_ascending_by_default()
    {
        Book::factory()->create(['book_title' => 'Чайка']);
        Book::factory()->create(['book_title' => 'Анна Каренина']);
        Book::factory()->create(['book_title' => 'Братья Карамазовы']);

        $response = $this->getJson('/api/books?per_page=10');

        $titles = collect($response->json('data'))->pluck('book_title');
        $this->assertEquals(
            ['Анна Каренина', 'Братья Карамазовы', 'Чайка'],
            $titles->toArray()
        );
    }

    public function test_index_sorts_by_book_title_descending()
    {
        Book::factory()->create(['book_title' => 'Чайка']);
        Book::factory()->create(['book_title' => 'Анна Каренина']);
        Book::factory()->create(['book_title' => 'Братья Карамазовы']);

        $response = $this->getJson('/api/books?sort=book_title&order=desc&per_page=10');

        $titles = collect($response->json('data'))->pluck('book_title');
        $this->assertEquals(
            ['Чайка', 'Братья Карамазовы', 'Анна Каренина'],
            $titles->toArray()
        );
    }

    public function test_index_sorts_by_published_year()
    {
        Book::factory()->create(['book_title' => 'Book C', 'published_year' => 2020]);
        Book::factory()->create(['book_title' => 'Book A', 'published_year' => 1990]);
        Book::factory()->create(['book_title' => 'Book B', 'published_year' => 2000]);

        $response = $this->getJson('/api/books?sort=published_year&order=asc&per_page=10');

        $titles = collect($response->json('data'))->pluck('book_title');
        $this->assertEquals(['Book A', 'Book B', 'Book C'], $titles->toArray());
    }

    public function test_index_sorts_by_created_at()
    {
        $book1 = Book::factory()->create(['created_at' => Carbon::now()->subDays(2)]);
        $book2 = Book::factory()->create(['created_at' => Carbon::now()->subDays(1)]);
        $book3 = Book::factory()->create(['created_at' => Carbon::now()]);

        $response = $this->getJson('/api/books?sort=created_at&order=asc&per_page=10');

        $ids = collect($response->json('data'))->pluck('book_id');
        $this->assertEquals(
            [$book1->book_id, $book2->book_id, $book3->book_id],
            $ids->map(fn($id) => (int) $id)->toArray()
        );
    }

    /**
     * Тесты обработки пустых результатов и ошибок страниц
     */

    public function test_index_returns_message_when_no_books_found_without_filters()
    {
        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'В библиотеке пока нет книг',
                'data' => [],
                'total' => 0
            ]);
    }

    public function test_index_returns_message_with_filters_when_no_results()
    {
        Genre::factory()->create(['genre_id' => 100, 'genre_name' => 'Тестовый жанр']);

        $response = $this->getJson('/api/books?genre_id=100&search=несуществующая+книга');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'total' => 0
            ]);
        $this->assertStringContainsString('По запросу', $response->json('message'));
        $this->assertStringContainsString('жанр: 100', $response->json('message'));
    }

    public function test_index_returns_404_when_page_exceeds_last_page()
    {
        Book::factory()->count(5)->create();

        $response = $this->getJson('/api/books?per_page=2&page=10');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Запрошенная страница не существует'
            ])
            ->assertJsonValidationErrors(['page']);
    }

    /**
     * Тесты метода show()
     */

    public function test_show_returns_400_for_non_numeric_id()
    {
        $response = $this->getJson('/api/books/abc');

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Некорректный ID книги'
            ]);
    }

    public function test_show_returns_400_for_negative_id()
    {
        $response = $this->getJson('/api/books/-5');

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Некорректный ID книги'
            ]);
    }

    public function test_show_returns_404_for_nonexistent_book()
    {
        $response = $this->getJson('/api/books/999999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Книга с ID 999999 не найдена'
            ]);
    }

    public function test_show_returns_book_detail_with_complete_structure()
    {
        $publisher = Publisher::factory()->create(['publisher_name' => 'АСТ']);
        $genre = Genre::factory()->create(['genre_name' => 'Классика']);
        $author = Author::factory()->create([
            'last_name' => 'Толстой',
            'first_name' => 'Лев',
            'middle_name' => 'Николаевич'
        ]);
        $format1 = Format::factory()->create(['format_name' => 'RTF']);
        $format2 = Format::factory()->create(['format_name' => 'EPUB']);

        $book = Book::factory()->create([
            'book_title' => 'Анна Каренина',
            'description' => 'Роман о любви и обществе',
            'published_year' => 1877,
            'cover_path' => 'covers/anna_karenina.png',
            'publisher_id' => $publisher->publisher_id
        ]);

        $book->genres()->attach($genre);
        $book->authors()->attach($author);

        BookFile::factory()->create([
            'book_id' => $book->book_id,
            'format_id' => $format1->format_id,
            'file_size_bytes' => 3145728,
            'file_path' => 'files/anna_karenina.rtf'
        ]);
        BookFile::factory()->create([
            'book_id' => $book->book_id,
            'format_id' => $format2->format_id,
            'file_size_bytes' => 1572864,
            'file_path' => 'files/anna_karenina.epub'
        ]);

        $response = $this->getJson("/api/books/{$book->book_id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'book_id',
                    'book_title',
                    'description',
                    'published_year',
                    'cover_url',
                    'is_favorited',
                    'genres',
                    'authors',
                    'publisher',
                    'files'
                ]
            ]);

        $data = $response->json('data');

        $this->assertEquals($book->book_id, $data['book_id']);
        $this->assertEquals('Анна Каренина', $data['book_title']);
        $this->assertEquals('Роман о любви и обществе', $data['description']);
        $this->assertEquals(1877, $data['published_year']);
        $this->assertEquals('/api/covers/anna_karenina.png', $data['cover_url']);
        $this->assertFalse($data['is_favorited']);

        $this->assertCount(1, $data['genres']);
        $this->assertEquals('Классика', $data['genres'][0]['genre_name']);

        $this->assertCount(1, $data['authors']);
        $this->assertEquals('Толстой', $data['authors'][0]['last_name']);
        $this->assertEquals('Лев', $data['authors'][0]['first_name']);
        $this->assertEquals('Николаевич', $data['authors'][0]['middle_name']);

        $this->assertEquals('АСТ', $data['publisher']['publisher_name']);

        $this->assertCount(2, $data['files']);
        $this->assertContains('RTF', collect($data['files'])->pluck('format_name'));
        $this->assertContains('EPUB', collect($data['files'])->pluck('format_name'));

        $pdfFile = collect($data['files'])->firstWhere('format_name', 'RTF');
        $this->assertEquals(3145728, $pdfFile['file_size_bytes']);
        $this->assertEquals(3.0, $pdfFile['file_size_mb']);
        $this->assertEquals('/api/books/file/' . $pdfFile['file_id'] . '/read', $pdfFile['read_url']);
        $this->assertEquals('/api/books/file/' . $pdfFile['file_id'] . '/download', $pdfFile['download_url']);
    }

    public function test_show_handles_book_without_cover_path()
    {
        $book = Book::factory()->create(['cover_path' => null]);

        $response = $this->getJson("/api/books/{$book->book_id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'cover_url' => null
            ]);
    }

    public function test_show_handles_book_without_files()
    {
        $book = Book::factory()->create();

        $response = $this->getJson("/api/books/{$book->book_id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'files' => []
            ]);
    }

    public function test_show_handles_book_without_genres()
    {
        $book = Book::factory()->create();

        $response = $this->getJson("/api/books/{$book->book_id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'genres' => []
            ]);
    }

    public function test_show_handles_book_without_authors()
    {
        $book = Book::factory()->create();

        $response = $this->getJson("/api/books/{$book->book_id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'authors' => []
            ]);
    }

    /**
     * Тесты авторизации и is_favorited
     */

    public function test_index_is_favorited_false_for_guest_user()
    {
        $book = Book::factory()->create();

        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'book_id' => $book->book_id,
                'is_favorited' => false
            ]);
    }

    /**
     * Тесты кодировки и спецсимволов
     */

    public function test_index_preserves_cyrillic_characters_in_response()
    {
        Book::factory()->create([
            'book_title' => 'Ёжик в тумане',
            'description' => 'Сказка о дружбе'
        ]);

        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'book_title' => 'Ёжик в тумане'
            ]);
    }

    public function test_index_handles_special_characters_in_search()
    {
        Book::factory()->create(['book_title' => 'C++ Programming']);
        Book::factory()->create(['book_title' => 'Python & Django']);

        $response = $this->getJson('/api/books?search=C%2B%2B');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['book_title' => 'C++ Programming']);
    }

    /**
     * Тесты производительности и нагрузки
     */

    public function test_index_handles_large_dataset_with_pagination()
    {
        Book::factory()->count(150)->create();

        $response = $this->getJson('/api/books?per_page=25&page=3');

        $response->assertStatus(200)
            ->assertJson([
                'current_page' => 3,
                'per_page' => 25,
                'total' => 150,
                'last_page' => 6
            ])
            ->assertJsonCount(25, 'data');
    }

    /**
     * Тесты дополнительных сценариев
     */

    public function test_index_default_sort_and_pagination_values()
    {
        Book::factory()->count(20)->create();

        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
            ->assertJson([
                'per_page' => 15,
                'current_page' => 1
            ]);
    }

    public function test_index_returns_unicode_encoded_response()
    {
        Book::factory()->create(['book_title' => 'Привет, мир!']);

        $response = $this->getJson('/api/books');

        $response->assertStatus(200);
        $this->assertStringContainsString('Привет, мир!', $response->getContent());
    }

    public function test_show_preserves_file_size_calculation_precision()
    {
        $book = Book::factory()->create();
        $format = Format::factory()->create();

        BookFile::factory()->create([
            'book_id' => $book->book_id,
            'format_id' => $format->format_id,
            'file_size_bytes' => 1234567
        ]);

        $response = $this->getJson("/api/books/{$book->book_id}");

        $file = $response->json('data.files')[0];
        $this->assertEquals(1234567, $file['file_size_bytes']);
        $this->assertEquals(1.18, $file['file_size_mb']);
    }
}