<?php

namespace Tests\Feature\Admin;

use App\Models\Author;
use App\Models\Book;
use App\Models\BookFile;
use App\Models\Format;
use App\Models\Genre;
use App\Models\Publisher;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\FormatSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected string $testStoragePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(FormatSeeder::class);

        $this->testStoragePath = storage_path('app/testing_' . uniqid());
        mkdir($this->testStoragePath, 0755, true);
        config(['filesystems.disks.local.root' => $this->testStoragePath]);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->testStoragePath)) {
            $this->deleteDirectory($this->testStoragePath);
        }
        parent::tearDown();
    }

    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) return;
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = "{$dir}/{$file}";
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    protected function getFormat(string $name): Format
    {
        return Format::firstOrCreate(['format_name' => $name]);
    }

    protected function createAdmin(): User
    {
        return User::factory()->create(['role_id' => 1]);
    }

    protected function createRegularUser(): User
    {
        return User::factory()->create(['role_id' => 2]);
    }

    // ==================== ТЕСТЫ МЕТОДА index() ====================

    public function test_index_requires_authentication()
    {
        $response = $this->getJson('/api/admin/books');
        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Требуется авторизация'
            ]);
    }

    public function test_index_requires_admin_role()
    {
        $user = $this->createRegularUser();
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/admin/books');
        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Доступ запрещен. Требуются права администратора.'
            ]);
    }

    public function test_index_returns_paginated_books_for_admin()
    {
        $admin = $this->createAdmin();
        $publisher = Publisher::factory()->create();
        $author = Author::factory()->create();
        $genre = Genre::factory()->create();
        
        $books = Book::factory()->count(3)->create(['publisher_id' => $publisher->publisher_id]);
        foreach ($books as $book) {
            $book->authors()->attach($author->author_id);
            $book->genres()->attach($genre->genre_id);
        }

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/admin/books?per_page=2');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'book_id', 'book_title', 'published_year', 'cover_url',
                        'genres' => [['genre_id', 'genre_name']],
                        'authors' => [['author_id', 'last_name', 'first_name', 'middle_name']],
                        'publisher' => ['publisher_id', 'publisher_name'],
                        'files_count'
                    ]
                ],
                'pagination' => [
                    'current_page', 'last_page', 'per_page', 'total',
                    'next_page_url', 'prev_page_url'
                ]
            ])
            ->assertJson(['success' => true])
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('pagination.per_page', 2);
    }

    public function test_index_validates_pagination_parameters()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/admin/books?page=-1');
        $response->assertStatus(422)
            ->assertJson(['success' => false, 'message' => 'Ошибка валидации параметров']);

        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/admin/books?per_page=150');
        $response->assertStatus(422)
            ->assertJson(['success' => false, 'message' => 'Ошибка валидации параметров']);
    }

    public function test_index_handles_empty_database()
    {
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/admin/books');
        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonCount(0, 'data')
            ->assertJsonPath('pagination.total', 0);
    }

    // ==================== ТЕСТЫ МЕТОДА store() ====================

    public function test_store_requires_authentication()
    {
        // Sanctum middleware возвращает 401 до входа в контроллер
        $response = $this->postJson('/api/admin/books', []);
        $response->assertStatus(401);
    }

    public function test_store_requires_admin_role()
    {
        $user = $this->createRegularUser();
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/admin/books', []);
        $response->assertStatus(403)
            ->assertJson(['success' => false, 'message' => 'Доступ запрещен. Требуются права администратора.']);
    }

    public function test_store_validates_required_fields()
    {
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/admin/books', []);
        $response->assertStatus(422)
            ->assertJson(['success' => false, 'message' => 'Ошибка валидации данных'])
            ->assertJsonValidationErrors([
                'book_title', 'description', 'published_year',
                'publisher_id', 'author_ids', 'genre_ids'
            ]);
    }

    public function test_store_validates_published_year_range()
    {
        $admin = $this->createAdmin();
        $publisher = Publisher::factory()->create();
        $author = Author::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/admin/books', [
            'book_title' => 'Test', 'description' => 'Desc',
            'published_year' => 1700,
            'publisher_id' => $publisher->publisher_id,
            'author_ids' => [$author->author_id],
            'genre_ids' => [$genre->genre_id]
        ]);
        $response->assertStatus(422)->assertJsonValidationErrors(['published_year']);

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/admin/books', [
            'book_title' => 'Test', 'description' => 'Desc',
            'published_year' => date('Y') + 10,
            'publisher_id' => $publisher->publisher_id,
            'author_ids' => [$author->author_id],
            'genre_ids' => [$genre->genre_id]
        ]);
        $response->assertStatus(422)->assertJsonValidationErrors(['published_year']);
    }

    public function test_store_validates_foreign_keys_exist()
    {
        $admin = $this->createAdmin();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/admin/books', [
            'book_title' => 'Test', 'description' => 'Desc',
            'published_year' => 2020,
            'publisher_id' => 999999,
            'author_ids' => [999999],
            'genre_ids' => [$genre->genre_id]
        ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['publisher_id', 'author_ids.0']);
    }

    public function test_store_creates_book_successfully()
    {
        $admin = $this->createAdmin();
        $publisher = Publisher::factory()->create();
        $author = Author::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/admin/books', [
            'book_title' => 'Тестовая книга',
            'description' => 'Описание тестовой книги',
            'published_year' => 2024,
            'publisher_id' => $publisher->publisher_id,
            'author_ids' => [$author->author_id],
            'genre_ids' => [$genre->genre_id]
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true, 'message' => 'Книга успешно создана'])
            ->assertJsonPath('data.book_title', 'Тестовая книга');

        $this->assertDatabaseHas('books', ['book_title' => 'Тестовая книга']);
        $bookId = $response->json('data.book_id');
        $this->assertDatabaseHas('book_authors', ['book_id' => $bookId, 'author_id' => $author->author_id]);
        $this->assertDatabaseHas('book_genres', ['book_id' => $bookId, 'genre_id' => $genre->genre_id]);
    }

    public function test_store_handles_cover_upload()
    {
        $admin = $this->createAdmin();
        $publisher = Publisher::factory()->create();
        $author = Author::factory()->create();
        $genre = Genre::factory()->create();

        // Используем create() вместо image() чтобы не требовать GD
        $coverFile = UploadedFile::fake()->create('cover.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($admin, 'sanctum')
            ->withHeaders(['Accept' => 'application/json'])
            ->post('/api/admin/books', [
                'book_title' => 'Book with Cover',
                'description' => 'Description',
                'published_year' => 2023,
                'publisher_id' => $publisher->publisher_id,
                'author_ids' => [$author->author_id],
                'genre_ids' => [$genre->genre_id],
                'cover' => $coverFile
            ]);

        $response->assertStatus(201);

        $book = Book::find($response->json('data.book_id'));
        $this->assertNotNull($book->cover_path);
        $this->assertStringContainsString('covers', $book->cover_path);
        $this->assertTrue(Storage::disk('local')->exists($book->cover_path));
        
        $expectedUrl = '/api/covers/' . basename($book->cover_path);
        $this->assertEquals($expectedUrl, $response->json('data.cover_url'));
    }

    public function test_store_handles_multiple_authors_and_genres()
    {
        $admin = $this->createAdmin();
        $publisher = Publisher::factory()->create();
        $authors = Author::factory()->count(3)->create();
        $genres = Genre::factory()->count(2)->create();

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/admin/books', [
            'book_title' => 'Multi-author Book',
            'description' => 'Description',
            'published_year' => 2022,
            'publisher_id' => $publisher->publisher_id,
            'author_ids' => $authors->pluck('author_id')->toArray(),
            'genre_ids' => $genres->pluck('genre_id')->toArray()
        ]);

        $response->assertStatus(201);
        $bookId = $response->json('data.book_id');
        
        foreach ($authors as $author) {
            $this->assertDatabaseHas('book_authors', ['book_id' => $bookId, 'author_id' => $author->author_id]);
        }
        foreach ($genres as $genre) {
            $this->assertDatabaseHas('book_genres', ['book_id' => $bookId, 'genre_id' => $genre->genre_id]);
        }
    }

    public function test_store_handles_file_uploads()
    {
        $admin = $this->createAdmin();
        $publisher = Publisher::factory()->create();
        $author = Author::factory()->create();
        $genre = Genre::factory()->create();
        $format = $this->getFormat('PDF');

        // Создаём файл без зависимости от GD
        $pdfFile = UploadedFile::fake()->create('book.pdf', 1, 'application/pdf');

        $response = $this->actingAs($admin, 'sanctum')
            ->post('/api/admin/books', [
                'book_title' => 'Book with Files',
                'description' => 'Description',
                'published_year' => 2024,
                'publisher_id' => $publisher->publisher_id,
                'author_ids' => [$author->author_id],
                'genre_ids' => [$genre->genre_id],
                'files' => [
                    [
                        'format_id' => $format->format_id,
                        'file' => $pdfFile
                    ]
                ]
            ]);

        $response->assertStatus(201);

        $bookId = $response->json('data.book_id');
        $this->assertDatabaseHas('book_files', ['book_id' => $bookId]);
        
        $bookFile = BookFile::where('book_id', $bookId)->first();
        $this->assertNotNull($bookFile);
        $this->assertEquals($format->format_id, $bookFile->format_id);
        $this->assertEquals($pdfFile->getSize(), $bookFile->file_size_bytes);
        $this->assertTrue(Storage::disk('local')->exists($bookFile->file_path));
    }

    public function test_store_rolls_back_on_error()
    {
        $admin = $this->createAdmin();
        $publisher = Publisher::factory()->create();

        // Валидация отработает до загрузки файла, т.к. author_ids невалидны
        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/admin/books', [
            'book_title' => 'Error Book',
            'description' => 'Description',
            'published_year' => 2024,
            'publisher_id' => $publisher->publisher_id,
            'author_ids' => [999999],
            'genre_ids' => []
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('books', ['book_title' => 'Error Book']);
    }

    public function test_store_validates_cover_file_type_and_size()
    {
        $admin = $this->createAdmin();
        $publisher = Publisher::factory()->create();
        $author = Author::factory()->create();
        $genre = Genre::factory()->create();

        // Неподдерживаемый тип
        $badFile = UploadedFile::fake()->create('cover.exe', 100, 'application/x-executable');

        $response = $this->actingAs($admin, 'sanctum')
            ->withHeaders(['Accept' => 'application/json'])
            ->post('/api/admin/books', [
                'book_title' => 'Test', 'description' => 'Desc',
                'published_year' => 2024,
                'publisher_id' => $publisher->publisher_id,
                'author_ids' => [$author->author_id],
                'genre_ids' => [$genre->genre_id],
                'cover' => $badFile
            ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['cover']);

        // Слишком большой файл (лимит 5120 KB)
        $largeFile = UploadedFile::fake()->create('large.jpg', 6000, 'image/jpeg');

        $response = $this->actingAs($admin, 'sanctum')
            ->withHeaders(['Accept' => 'application/json'])
            ->post('/api/admin/books', [
                'book_title' => 'Test', 'description' => 'Desc',
                'published_year' => 2024,
                'publisher_id' => $publisher->publisher_id,
                'author_ids' => [$author->author_id],
                'genre_ids' => [$genre->genre_id],
                'cover' => $largeFile
            ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['cover']);
    }

    // ==================== ТЕСТЫ МЕТОДА show() ====================

    public function test_show_requires_authentication()
    {
        $book = Book::factory()->create();
        $response = $this->getJson("/api/admin/books/{$book->book_id}");
        $response->assertStatus(401)
            ->assertJson(['success' => false, 'message' => 'Требуется авторизация']);
    }

    public function test_show_requires_admin_role()
    {
        $user = $this->createRegularUser();
        $book = Book::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->getJson("/api/admin/books/{$book->book_id}");
        $response->assertStatus(403)
            ->assertJson(['success' => false, 'message' => 'Доступ запрещен. Требуются права администратора.']);
    }

    public function test_show_returns_404_for_nonexistent_book()
    {
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin, 'sanctum')->getJson('/api/admin/books/999999');
        $response->assertStatus(404)
            ->assertJson(['success' => false, 'message' => 'Книга с указанным ID не найдена']);
    }

    public function test_show_returns_book_with_full_details()
    {
        $admin = $this->createAdmin();
        $publisher = Publisher::factory()->create();
        $author = Author::factory()->create();
        $genre = Genre::factory()->create();
        $format = $this->getFormat('FB2');

        $book = Book::factory()->create([
            'publisher_id' => $publisher->publisher_id,
            'book_title' => 'Детальная книга',
            'description' => 'Полное описание',
            'published_year' => 2023
        ]);
        $book->authors()->attach($author->author_id);
        $book->genres()->attach($genre->genre_id);
        $bookFile = $book->files()->create([
            'format_id' => $format->format_id,
            'file_path' => 'books/detail.fb2',
            'file_size_bytes' => 2048
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson("/api/admin/books/{$book->book_id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.book_id', $book->book_id)
            ->assertJsonPath('data.book_title', 'Детальная книга');

        $fileData = $response->json('data.files.0');
        $this->assertEquals(0.0, $fileData['file_size_mb']);
        $this->assertEquals('/api/books/file/' . $bookFile->file_id . '/read', $fileData['read_url']);
    }

    public function test_show_handles_book_without_cover()
    {
        $admin = $this->createAdmin();
        $book = Book::factory()->create(['cover_path' => null]);
        $response = $this->actingAs($admin, 'sanctum')->getJson("/api/admin/books/{$book->book_id}");
        $response->assertStatus(200)->assertJsonPath('data.cover_url', null);
    }

    // ==================== ТЕСТЫ МЕТОДА update() ====================

    public function test_update_requires_authentication()
    {
        $book = Book::factory()->create();
        $response = $this->putJson("/api/admin/books/{$book->book_id}", []);
        $response->assertStatus(401);
    }

    public function test_update_requires_admin_role()
    {
        $user = $this->createRegularUser();
        $book = Book::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->putJson("/api/admin/books/{$book->book_id}", []);
        $response->assertStatus(403)
            ->assertJson(['success' => false, 'message' => 'Доступ запрещен. Требуются права администратора.']);
    }

    public function test_update_returns_404_for_nonexistent_book()
    {
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin, 'sanctum')
            ->putJson('/api/admin/books/999999', ['book_title' => 'New Title']);
        $response->assertStatus(404)
            ->assertJson(['success' => false, 'message' => 'Книга с указанным ID не найдена']);
    }

    public function test_update_modifies_book_fields()
    {
        $admin = $this->createAdmin();
        $publisher = Publisher::factory()->create();
        $newPublisher = Publisher::factory()->create();
        $book = Book::factory()->create(['publisher_id' => $publisher->publisher_id]);

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/admin/books/{$book->book_id}", [
                'book_title' => 'Обновлённое название',
                'description' => 'Новое описание',
                'published_year' => 2025,
                'publisher_id' => $newPublisher->publisher_id
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Книга обновлена'])
            ->assertJsonPath('data.book_title', 'Обновлённое название');

        $this->assertDatabaseHas('books', [
            'book_id' => $book->book_id,
            'book_title' => 'Обновлённое название',
            'publisher_id' => $newPublisher->publisher_id
        ]);
    }

    public function test_update_replaces_cover_and_deletes_old()
    {
        $admin = $this->createAdmin();
        $publisher = Publisher::factory()->create();
        $book = Book::factory()->create(['publisher_id' => $publisher->publisher_id]);
        
        $oldCoverPath = 'covers/old_cover.jpg';
        Storage::disk('local')->put($oldCoverPath, 'old_content');
        $book->cover_path = $oldCoverPath;
        $book->save();

        $newCover = UploadedFile::fake()->create('new_cover.png', 100, 'image/png');

        $response = $this->actingAs($admin, 'sanctum')
            ->withHeaders(['Accept' => 'application/json'])
            ->post("/api/admin/books/{$book->book_id}", [
                '_method' => 'PUT',
                'cover' => $newCover
            ]);

        $response->assertStatus(200);
        $book->refresh();
        
        $this->assertFalse(Storage::disk('local')->exists($oldCoverPath));
        $this->assertTrue(Storage::disk('local')->exists($book->cover_path));
        $this->assertStringContainsString('.png', $book->cover_path);
    }

    public function test_update_syncs_authors_and_genres()
    {
        $admin = $this->createAdmin();
        $publisher = Publisher::factory()->create();
        
        $oldAuthor = Author::factory()->create();
        $newAuthor = Author::factory()->create();
        $oldGenre = Genre::factory()->create();
        $newGenre = Genre::factory()->create();

        $book = Book::factory()->create(['publisher_id' => $publisher->publisher_id]);
        $book->authors()->attach($oldAuthor->author_id);
        $book->genres()->attach($oldGenre->genre_id);

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/admin/books/{$book->book_id}", [
                'book_title' => $book->book_title,
                'description' => $book->description,
                'published_year' => $book->published_year,
                'publisher_id' => $publisher->publisher_id,
                'author_ids' => [$newAuthor->author_id],
                'genre_ids' => [$newGenre->genre_id]
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('book_authors', ['book_id' => $book->book_id, 'author_id' => $oldAuthor->author_id]);
        $this->assertDatabaseHas('book_authors', ['book_id' => $book->book_id, 'author_id' => $newAuthor->author_id]);
        $this->assertDatabaseMissing('book_genres', ['book_id' => $book->book_id, 'genre_id' => $oldGenre->genre_id]);
        $this->assertDatabaseHas('book_genres', ['book_id' => $book->book_id, 'genre_id' => $newGenre->genre_id]);
    }

    public function test_update_adds_new_files_without_removing_existing()
    {
        $admin = $this->createAdmin();
        $publisher = Publisher::factory()->create();
        $format1 = $this->getFormat('PDF');
        $format2 = $this->getFormat('FB2');

        $book = Book::factory()->create(['publisher_id' => $publisher->publisher_id]);
        $existingFile = $book->files()->create([
            'format_id' => $format1->format_id,
            'file_path' => 'books/existing.pdf',
            'file_size_bytes' => 1000
        ]);

        $newFile = UploadedFile::fake()->create('new.fb2', 500, 'application/octet-stream');

        $response = $this->actingAs($admin, 'sanctum')
            ->withHeaders(['Accept' => 'application/json'])
            ->post("/api/admin/books/{$book->book_id}", [
                '_method' => 'PUT',
                'book_title' => $book->book_title,
                'description' => $book->description,
                'published_year' => $book->published_year,
                'publisher_id' => $publisher->publisher_id,
                'files' => [
                    0 => [
                        'format_id' => $format2->format_id,
                        'file' => $newFile
                    ]
                ]
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('book_files', ['file_id' => $existingFile->file_id]);
        $this->assertDatabaseCount('book_files', 2);
        
        $newBookFile = BookFile::where('book_id', $book->book_id)
            ->where('file_id', '!=', $existingFile->file_id)
            ->first();
        $this->assertNotNull($newBookFile);
        $this->assertEquals($format2->format_id, $newBookFile->format_id);
    }

    // ==================== ТЕСТЫ МЕТОДА destroy() ====================

    public function test_destroy_requires_authentication()
    {
        $book = Book::factory()->create();
        $response = $this->deleteJson("/api/admin/books/{$book->book_id}");
        $response->assertStatus(401)
            ->assertJson(['success' => false, 'message' => 'Требуется авторизация']);
    }

    public function test_destroy_requires_admin_role()
    {
        $user = $this->createRegularUser();
        $book = Book::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/admin/books/{$book->book_id}");
        $response->assertStatus(403)
            ->assertJson(['success' => false, 'message' => 'Доступ запрещен. Требуются права администратора.']);
    }

    public function test_destroy_returns_404_for_nonexistent_book()
    {
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin, 'sanctum')->deleteJson('/api/admin/books/999999');
        $response->assertStatus(404)
            ->assertJson(['success' => false, 'message' => 'Книга с указанным ID не найдена']);
    }

    public function test_destroy_deletes_book_and_cleans_files()
    {
        $admin = $this->createAdmin();
        $publisher = Publisher::factory()->create();
        $author = Author::factory()->create();
        $genre = Genre::factory()->create();
        $format = $this->getFormat('PDF');

        $book = Book::factory()->create(['publisher_id' => $publisher->publisher_id]);
        
        $coverPath = 'covers/test_cover.jpg';
        Storage::disk('local')->put($coverPath, 'cover_content');
        $book->cover_path = $coverPath;
        $book->save();

        $filePath = 'books/test_file.pdf';
        Storage::disk('local')->put($filePath, 'file_content');
        $bookFile = $book->files()->create([
            'format_id' => $format->format_id,
            'file_path' => $filePath,
            'file_size_bytes' => 1024
        ]);

        $book->authors()->attach($author->author_id);
        $book->genres()->attach($genre->genre_id);

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/admin/books/{$book->book_id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'Книга успешно удалена']);

        $this->assertDatabaseMissing('books', ['book_id' => $book->book_id]);
        $this->assertFalse(Storage::disk('local')->exists($coverPath));
        $this->assertFalse(Storage::disk('local')->exists($filePath));
        $this->assertDatabaseMissing('book_authors', ['book_id' => $book->book_id]);
        $this->assertDatabaseMissing('book_genres', ['book_id' => $book->book_id]);
        $this->assertDatabaseMissing('book_files', ['book_id' => $book->book_id]);
    }

    public function test_destroy_handles_missing_files_gracefully()
    {
        $admin = $this->createAdmin();
        $book = Book::factory()->create();
        
        $book->cover_path = 'covers/missing.jpg';
        $book->save();
        
        $book->files()->create([
            'format_id' => $this->getFormat('PDF')->format_id,
            'file_path' => 'books/missing.pdf',
            'file_size_bytes' => 100
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/admin/books/{$book->book_id}");

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    // ==================== ИНТЕГРАЦИОННЫЕ ТЕСТЫ ====================

    public function test_full_crud_workflow()
    {
        $admin = $this->createAdmin();
        $publisher = Publisher::factory()->create();
        $author = Author::factory()->create();
        $genre = Genre::factory()->create();

        // CREATE
        $createResponse = $this->actingAs($admin, 'sanctum')->postJson('/api/admin/books', [
            'book_title' => 'Workflow Book',
            'description' => 'For CRUD test',
            'published_year' => 2024,
            'publisher_id' => $publisher->publisher_id,
            'author_ids' => [$author->author_id],
            'genre_ids' => [$genre->genre_id]
        ]);
        $createResponse->assertStatus(201);
        $bookId = $createResponse->json('data.book_id');

        // READ
        $readResponse = $this->actingAs($admin, 'sanctum')->getJson("/api/admin/books/{$bookId}");
        $readResponse->assertStatus(200)->assertJsonPath('data.book_title', 'Workflow Book');

        // UPDATE
        $updateResponse = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/admin/books/{$bookId}", [
                'book_title' => 'Updated Workflow Book',
                'description' => 'For CRUD test',
                'published_year' => 2024,
                'publisher_id' => $publisher->publisher_id,
                'author_ids' => [$author->author_id],
                'genre_ids' => [$genre->genre_id]
            ]);
        $updateResponse->assertStatus(200)->assertJsonPath('data.book_title', 'Updated Workflow Book');

        // INDEX
        $indexResponse = $this->actingAs($admin, 'sanctum')->getJson('/api/admin/books');
        $indexResponse->assertStatus(200);
        $titles = collect($indexResponse->json('data'))->pluck('book_title');
        $this->assertTrue($titles->contains('Updated Workflow Book'));

        // DELETE
        $deleteResponse = $this->actingAs($admin, 'sanctum')->deleteJson("/api/admin/books/{$bookId}");
        $deleteResponse->assertStatus(200);

        $this->assertDatabaseMissing('books', ['book_id' => $bookId]);
    }

    public function test_cyrillic_data_handling()
    {
        $admin = $this->createAdmin();
        $publisher = Publisher::factory()->create(['publisher_name' => 'Издательство «Наука»']);
        $author = Author::factory()->create([
            'last_name' => 'Достоевский',
            'first_name' => 'Фёдор',
            'middle_name' => 'Михайлович'
        ]);
        $genre = Genre::factory()->create(['genre_name' => 'Классическая литература']);

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/admin/books', [
            'book_title' => 'Преступление и наказание',
            'description' => 'Роман о духовном перерождении',
            'published_year' => 1866,
            'publisher_id' => $publisher->publisher_id,
            'author_ids' => [$author->author_id],
            'genre_ids' => [$genre->genre_id]
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true], JSON_UNESCAPED_UNICODE)
            ->assertJsonPath('data.book_title', 'Преступление и наказание');

        $bookId = $response->json('data.book_id');
        $showResponse = $this->actingAs($admin, 'sanctum')->getJson("/api/admin/books/{$bookId}");
        $showResponse->assertStatus(200)->assertJson(['success' => true], JSON_UNESCAPED_UNICODE);
        
        $data = $showResponse->json('data');
        $this->assertEquals('Фёдор', $data['authors'][0]['first_name']);
        $this->assertEquals('Классическая литература', $data['genres'][0]['genre_name']);
        $this->assertEquals('Издательство «Наука»', $data['publisher']['publisher_name']);
    }

    public function test_unicode_in_file_names()
    {
        $admin = $this->createAdmin();
        $publisher = Publisher::factory()->create();
        $author = Author::factory()->create();
        $genre = Genre::factory()->create();
        $format = $this->getFormat('PDF');

        // Используем create() вместо image() для обложки
        $coverFile = UploadedFile::fake()->create('обложка_книги.jpg', 100, 'image/jpeg');
        $bookFile = UploadedFile::fake()->create('книга_текст.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($admin, 'sanctum')
            ->withHeaders(['Accept' => 'application/json'])
            ->post('/api/admin/books', [
                'book_title' => 'Unicode Test',
                'description' => 'Test',
                'published_year' => 2024,
                'publisher_id' => $publisher->publisher_id,
                'author_ids' => [$author->author_id],
                'genre_ids' => [$genre->genre_id],
                'cover' => $coverFile,
                'files' => [
                    0 => ['format_id' => $format->format_id, 'file' => $bookFile]
                ]
            ]);

        $response->assertStatus(201);
        $book = Book::find($response->json('data.book_id'));
        $this->assertNotNull($book->cover_path);
        $this->assertTrue(Storage::disk('local')->exists($book->cover_path));
        $this->assertDatabaseHas('book_files', ['book_id' => $book->book_id]);
    }
}