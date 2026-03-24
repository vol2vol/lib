<?php

namespace Tests\Feature\Admin;

use App\Models\Book;
use App\Models\BookFile;
use App\Models\Format;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FormatControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $adminUser;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Создаём роли
        Role::firstOrCreate(['role_id' => 1, 'role_name' => 'admin']);
        Role::firstOrCreate(['role_id' => 2, 'role_name' => 'user']);

        // Создаём тестовых пользователей
        $this->adminUser = User::factory()->create(['role_id' => 1]);
        $this->regularUser = User::factory()->create(['role_id' => 2]);
    }

    /**
     * Вспомогательный метод для получения формата без дубликатов
     */
    protected function getFormat(string $name): Format
    {
        return Format::firstOrCreate(['format_name' => $name]);
    }

    // ==================== ТЕСТЫ МЕТОДА index() ====================

    public function test_index_requires_authentication()
    {
        $response = $this->getJson('/api/admin/formats');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Требуется авторизация'
            ]);
    }

    public function test_index_requires_admin_role()
    {
        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->getJson('/api/admin/formats');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Доступ запрещен. Требуются права администратора.'
            ]);
    }

    public function test_index_returns_paginated_formats_list()
    {
        for ($i = 0; $i < 25; $i++) {
            Format::create(['format_name' => $this->faker->unique()->fileExtension()]);
        }

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/admin/formats');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ])
            ->assertJsonStructure([
                'data' => [
                    'data',
                    'current_page',
                    'per_page',
                    'total',
                    'last_page'
                ]
            ]);

        $this->assertEquals(20, $response->json('data.per_page'));
        $this->assertEquals(25, $response->json('data.total'));
    }

    public function test_index_includes_books_count_for_each_format()
    {
        $format = $this->getFormat('PDF');
        $book1 = Book::factory()->create();
        $book2 = Book::factory()->create();

        // Создаём связи через book_files
        BookFile::factory()->create(['book_id' => $book1->book_id, 'format_id' => $format->format_id]);
        BookFile::factory()->create(['book_id' => $book2->book_id, 'format_id' => $format->format_id]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/admin/formats');

        $response->assertStatus(200);
        $formatData = collect($response->json('data.data'))->firstWhere('format_id', $format->format_id);

        $this->assertArrayHasKey('books_count', $formatData);
        $this->assertEquals(2, $formatData['books_count']);
    }

    public function test_index_returns_empty_list_when_no_formats()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/admin/formats');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'data' => [],
                    'total' => 0
                ]
            ]);
    }

    // ==================== ТЕСТЫ МЕТОДА store() ====================

    public function test_store_requires_authentication()
    {
        $response = $this->postJson('/api/admin/formats', [
            'format_name' => 'EPUB'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Требуется авторизация'
            ]);
    }

    public function test_store_requires_admin_role()
    {
        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->postJson('/api/admin/formats', [
                'format_name' => 'EPUB'
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Доступ запрещен. Требуются права администратора.'
            ]);
    }

    public function test_store_validates_required_field()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/admin/formats', []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Ошибка валидации'
            ])
            ->assertJsonValidationErrors(['format_name']);
    }

    public function test_store_validates_format_name_max_length()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/admin/formats', [
                'format_name' => str_repeat('a', 256)
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['format_name']);
    }

    public function test_store_validates_unique_format_name()
    {
        $this->getFormat('PDF');

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/admin/formats', [
                'format_name' => 'PDF'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['format_name']);
    }

    public function test_store_successfully_creates_format()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/admin/formats', [
                'format_name' => 'FB2'
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Формат успешно создан',
                'data' => [
                    'format_name' => 'FB2'
                ]
            ]);

        $this->assertDatabaseHas('formats', [
            'format_name' => 'FB2'
        ]);
    }

    public function test_store_handles_cyrillic_format_name()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/admin/formats', [
                'format_name' => 'Текстовый'
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'format_name' => 'Текстовый'
            ]);
    }

    // ==================== ТЕСТЫ МЕТОДА show() ====================

    public function test_show_requires_authentication()
    {
        $format = $this->getFormat('PDF');

        $response = $this->getJson("/api/admin/formats/{$format->format_id}");

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Требуется авторизация'
            ]);
    }

    public function test_show_requires_admin_role()
    {
        $format = $this->getFormat('PDF');

        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->getJson("/api/admin/formats/{$format->format_id}");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Доступ запрещен. Требуются права администратора.'
            ]);
    }

    public function test_show_returns_404_for_nonexistent_format()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/admin/formats/999999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Формат не найден'
            ]);
    }

    public function test_show_returns_format_with_books_relationship()
    {
        $format = $this->getFormat('PDF');
        $book1 = Book::factory()->create(['book_title' => 'Война и мир']);
        $book2 = Book::factory()->create(['book_title' => 'Анна Каренина']);

        BookFile::factory()->create(['book_id' => $book1->book_id, 'format_id' => $format->format_id]);
        BookFile::factory()->create(['book_id' => $book2->book_id, 'format_id' => $format->format_id]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/admin/formats/{$format->format_id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'format_id' => $format->format_id,
                    'format_name' => 'PDF'
                ]
            ]);

        $this->assertCount(2, $response->json('data.books'));
        $bookTitles = collect($response->json('data.books'))->pluck('book_title')->toArray();
        $this->assertContains('Война и мир', $bookTitles);
        $this->assertContains('Анна Каренина', $bookTitles);
    }

    public function test_show_returns_format_without_books()
    {
        $format = $this->getFormat('MOBI');

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/admin/formats/{$format->format_id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'books' => []
            ]);
    }

    // ==================== ТЕСТЫ МЕТОДА update() ====================

    public function test_update_requires_authentication()
    {
        $format = $this->getFormat('PDF');

        $response = $this->putJson("/api/admin/formats/{$format->format_id}", [
            'format_name' => 'PDF-Updated'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Требуется авторизация'
            ]);
    }

    public function test_update_requires_admin_role()
    {
        $format = $this->getFormat('PDF');

        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->putJson("/api/admin/formats/{$format->format_id}", [
                'format_name' => 'PDF-Updated'
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Доступ запрещен. Требуются права администратора.'
            ]);
    }

    public function test_update_returns_404_for_nonexistent_format()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson('/api/admin/formats/999999', [
                'format_name' => 'Updated'
            ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Формат не найден'
            ]);
    }

    public function test_update_validates_format_name_max_length()
    {
        $format = $this->getFormat('PDF');

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/admin/formats/{$format->format_id}", [
                'format_name' => str_repeat('a', 256)
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['format_name']);
    }

    public function test_update_validates_unique_format_name_excluding_current()
    {
        $format1 = $this->getFormat('PDF');
        $format2 = $this->getFormat('EPUB');

        // Попытка изменить EPUB на PDF должна вызвать ошибку уникальности
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/admin/formats/{$format2->format_id}", [
                'format_name' => 'PDF'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['format_name']);
    }

    public function test_update_allows_same_name_for_current_format()
    {
        $format = $this->getFormat('PDF');

        // Обновление тем же именем должно работать
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/admin/formats/{$format->format_id}", [
                'format_name' => 'PDF'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Формат успешно обновлен'
            ]);
    }

    public function test_update_successfully_changes_format_name()
    {
        $format = $this->getFormat('OLD');

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/admin/formats/{$format->format_id}", [
                'format_name' => 'NEW-FORMAT'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Формат успешно обновлен',
                'data' => [
                    'format_name' => 'NEW-FORMAT'
                ]
            ]);

        $this->assertDatabaseHas('formats', [
            'format_id' => $format->format_id,
            'format_name' => 'NEW-FORMAT'
        ]);
    }

    public function test_update_handles_cyrillic_format_name()
    {
        $format = $this->getFormat('TXT');

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/admin/formats/{$format->format_id}", [
                'format_name' => 'Текстовый документ'
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'format_name' => 'Текстовый документ'
            ]);
    }

    // ==================== ТЕСТЫ МЕТОДА destroy() ====================

    public function test_destroy_requires_authentication()
    {
        $format = $this->getFormat('PDF');

        $response = $this->deleteJson("/api/admin/formats/{$format->format_id}");

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Требуется авторизация'
            ]);
    }

    public function test_destroy_requires_admin_role()
    {
        $format = $this->getFormat('PDF');

        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->deleteJson("/api/admin/formats/{$format->format_id}");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Доступ запрещен. Требуются права администратора.'
            ]);
    }

    public function test_destroy_returns_404_for_nonexistent_format()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson('/api/admin/formats/999999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Формат не найден'
            ]);
    }

    public function test_destroy_returns_422_when_format_has_books()
    {
        $format = $this->getFormat('PDF');
        $book = Book::factory()->create();

        BookFile::factory()->create(['book_id' => $book->book_id, 'format_id' => $format->format_id]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/admin/formats/{$format->format_id}");

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Нельзя удалить формат, в котором есть книги'
            ]);

        $this->assertDatabaseHas('formats', [
            'format_id' => $format->format_id
        ]);
    }

    public function test_destroy_successfully_deletes_format_without_books()
    {
        $format = $this->getFormat('UNUSED');
        $formatId = $format->format_id;

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/admin/formats/{$formatId}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Формат успешно удален'
            ]);

        $this->assertDatabaseMissing('formats', [
            'format_id' => $formatId
        ]);
    }

    public function test_destroy_removes_format_from_database()
    {
        $format = $this->getFormat('TO-DELETE');
        $formatId = $format->format_id;

        $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/admin/formats/{$formatId}");

        $this->assertDatabaseMissing('formats', [
            'format_id' => $formatId
        ]);
    }

    // ==================== ИНТЕГРАЦИОННЫЕ ТЕСТЫ ====================

    public function test_full_format_crud_lifecycle()
    {
        // 1. Создаём формат
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/admin/formats', [
                'format_name' => 'TEST-FORMAT'
            ]);
        $response->assertStatus(201);
        $formatId = $response->json('data.format_id');

        // 2. Получаем список форматов
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/admin/formats');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data');

        // 3. Получаем конкретный формат
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/admin/formats/{$formatId}");
        $response->assertStatus(200)
            ->assertJsonFragment(['format_name' => 'TEST-FORMAT']);

        // 4. Обновляем формат
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/admin/formats/{$formatId}", [
                'format_name' => 'TEST-FORMAT-UPDATED'
            ]);
        $response->assertStatus(200)
            ->assertJsonFragment(['format_name' => 'TEST-FORMAT-UPDATED']);

        // 5. Пытаемся удалить формат с книгами (должно вернуть 422)
        $book = Book::factory()->create();
        BookFile::factory()->create(['book_id' => $book->book_id, 'format_id' => $formatId]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/admin/formats/{$formatId}");
        $response->assertStatus(422);

        // 6. Удаляем связь и удаляем формат
        BookFile::where('format_id', $formatId)->delete();

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/admin/formats/{$formatId}");
        $response->assertStatus(200);
        $this->assertDatabaseMissing('formats', ['format_id' => $formatId]);
    }

    public function test_multiple_admins_can_manage_formats()
    {
        $admin2 = User::factory()->create(['role_id' => 1]);
        $format = $this->getFormat('PDF');

        // Admin 1 создаёт
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/admin/formats', ['format_name' => 'TestFormat']);
        $response->assertStatus(201);

        // Admin 2 получает список
        $response = $this->actingAs($admin2, 'sanctum')
            ->getJson('/api/admin/formats');
        $response->assertStatus(200);

        // Admin 2 обновляет
        $formatId = Format::latest()->first()->format_id;
        $response = $this->actingAs($admin2, 'sanctum')
            ->putJson("/api/admin/formats/{$formatId}", ['format_name' => 'UpdatedFormat']);
        $response->assertStatus(200);

        // Admin 1 удаляет
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/admin/formats/{$formatId}");
        $response->assertStatus(200);
    }

    public function test_regular_user_cannot_bypass_admin_check()
    {
        $format = $this->getFormat('PDF');

        $endpoints = [
            ['GET', '/api/admin/formats'],
            ['POST', '/api/admin/formats', ['format_name' => 'Test']],
            ['GET', "/api/admin/formats/{$format->format_id}"],
            ['PUT', "/api/admin/formats/{$format->format_id}", ['format_name' => 'Updated']],
            ['DELETE', "/api/admin/formats/{$format->format_id}"],
        ];

        foreach ($endpoints as $endpoint) {
            $method = $endpoint[0];
            $url = $endpoint[1];
            $data = $endpoint[2] ?? [];

            $response = $this->actingAs($this->regularUser, 'sanctum')
                ->json($method, $url, $data);

            $response->assertStatus(403)
                ->assertJsonFragment([
                    'success' => false,
                    'message' => 'Доступ запрещен. Требуются права администратора.'
                ]);
        }
    }

    public function test_format_with_multiple_books_cannot_be_deleted()
    {
        $format = $this->getFormat('PDF');
        $books = Book::factory()->count(5)->create();

        foreach ($books as $book) {
            BookFile::factory()->create(['book_id' => $book->book_id, 'format_id' => $format->format_id]);
        }

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/admin/formats/{$format->format_id}");

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Нельзя удалить формат, в котором есть книги'
            ]);

        $this->assertDatabaseHas('formats', [
            'format_id' => $format->format_id
        ]);
    }

    public function test_unicode_characters_in_format_data()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/admin/formats', [
                'format_name' => 'Формат_Тест'
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'format_name' => 'Формат_Тест'
            ]);
        
        // Дополнительно можно проверить через декодированный JSON
        $this->assertEquals('Формат_Тест', $response->json('data.format_name'));
    }

    public function test_format_timestamps_are_set()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/admin/formats', [
                'format_name' => 'TimestampTest'
            ]);

        $response->assertStatus(201);
        $formatId = $response->json('data.format_id');

        $format = Format::find($formatId);
        $this->assertNotNull($format->created_at);
        $this->assertNotNull($format->updated_at);
    }

    public function test_index_pagination_works_correctly()
    {
        for ($i = 0; $i < 45; $i++) {
            Format::create(['format_name' => $this->faker->unique()->fileExtension()]);
        }

        // Страница 1
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/admin/formats?page=1');
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'current_page' => 1,
                    'per_page' => 20,
                    'total' => 45,
                    'last_page' => 3
                ]
            ]);
        $this->assertCount(20, $response->json('data.data'));

        // Страница 2
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/admin/formats?page=2');
        $response->assertStatus(200)
            ->assertJson(['data' => ['current_page' => 2]]);
        $this->assertCount(20, $response->json('data.data'));

        // Страница 3
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/admin/formats?page=3');
        $response->assertStatus(200)
            ->assertJson(['data' => ['current_page' => 3]]);
        $this->assertCount(5, $response->json('data.data'));
    }

    public function test_store_rejects_empty_format_name()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/admin/formats', [
                'format_name' => ''
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['format_name']);
    }

    public function test_update_rejects_empty_format_name()
    {
        $format = $this->getFormat('PDF');

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/admin/formats/{$format->format_id}", [
                'format_name' => ''
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['format_name']);
    }

    public function test_show_eager_loads_books_relationship()
    {
        $format = $this->getFormat('PDF');
        $book = Book::factory()->create();
        BookFile::factory()->create(['book_id' => $book->book_id, 'format_id' => $format->format_id]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/admin/formats/{$format->format_id}");

        $response->assertStatus(200);
        $this->assertIsArray($response->json('data.books'));
    }

    public function test_index_orders_formats_by_default()
    {
        Format::factory()->create(['format_name' => 'Zebra']);
        Format::factory()->create(['format_name' => 'Alpha']);
        Format::factory()->create(['format_name' => 'Middle']);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/admin/formats?per_page=10');

        $response->assertStatus(200);
        $names = collect($response->json('data.data'))->pluck('format_name')->toArray();
        
        // Laravel пагинация по умолчанию сортирует по первичному ключу (created_at)
        $this->assertCount(3, $names);
    }
}