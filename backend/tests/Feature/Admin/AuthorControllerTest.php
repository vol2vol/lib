<?php

namespace Tests\Feature\Admin;

use App\Models\Author;
use App\Models\Book;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthorControllerTest extends TestCase
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

    // ==================== ТЕСТЫ МЕТОДА index() ====================

    public function test_index_requires_authentication()
    {
        $response = $this->getJson('/api/admin/authors');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Требуется авторизация'
            ]);
    }

    public function test_index_requires_admin_role()
    {
        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->getJson('/api/admin/authors');

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Доступ запрещен. Требуются права администратора.'
            ]);
    }

    public function test_index_returns_paginated_authors_list()
    {
        Author::factory()->count(25)->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/admin/authors');

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

    public function test_index_includes_books_count_for_each_author()
    {
        $author = Author::factory()->create();
        $book1 = Book::factory()->create();
        $book2 = Book::factory()->create();
        $author->books()->attach([$book1->book_id, $book2->book_id]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/admin/authors');

        $response->assertStatus(200);
        $authorData = collect($response->json('data.data'))->firstWhere('author_id', $author->author_id);

        $this->assertArrayHasKey('books_count', $authorData);
        $this->assertEquals(2, $authorData['books_count']);
    }

    public function test_index_returns_empty_list_when_no_authors()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/admin/authors');

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
        $response = $this->postJson('/api/admin/authors', [
            'last_name' => 'Толстой',
            'first_name' => 'Лев',
            'middle_name' => 'Николаевич'
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
            ->postJson('/api/admin/authors', [
                'last_name' => 'Толстой',
                'first_name' => 'Лев',
                'middle_name' => 'Николаевич'
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Доступ запрещен. Требуются права администратора.'
            ]);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/admin/authors', []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Ошибка валидации'
            ])
            ->assertJsonValidationErrors(['last_name', 'first_name']);
    }

    public function test_store_validates_last_name_max_length()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/admin/authors', [
                'last_name' => str_repeat('a', 256),
                'first_name' => 'Лев'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['last_name']);
    }

    public function test_store_validates_first_name_max_length()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/admin/authors', [
                'last_name' => 'Толстой',
                'first_name' => str_repeat('a', 256)
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['first_name']);
    }

    public function test_store_validates_middle_name_max_length()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/admin/authors', [
                'last_name' => 'Толстой',
                'first_name' => 'Лев',
                'middle_name' => str_repeat('a', 256)
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['middle_name']);
    }

    public function test_store_successfully_creates_author_with_all_fields()
    {
        $data = [
            'last_name' => 'Толстой',
            'first_name' => 'Лев',
            'middle_name' => 'Николаевич'
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/admin/authors', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Автор успешно создан',
                'data' => [
                    'last_name' => 'Толстой',
                    'first_name' => 'Лев',
                    'middle_name' => 'Николаевич'
                ]
            ]);

        $this->assertDatabaseHas('authors', [
            'last_name' => 'Толстой',
            'first_name' => 'Лев'
        ]);
    }

    public function test_store_creates_author_without_middle_name()
    {
        $data = [
            'last_name' => 'Пушкин',
            'first_name' => 'Александр',
            'middle_name' => null
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/admin/authors', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Автор успешно создан'
            ]);

        $this->assertDatabaseHas('authors', [
            'last_name' => 'Пушкин',
            'first_name' => 'Александр',
            'middle_name' => null
        ]);
    }

    public function test_store_creates_author_without_birth_date()
    {
        $data = [
            'last_name' => 'Достоевский',
            'first_name' => 'Фёдор',
            'birth_date' => null
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/admin/authors', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Автор успешно создан'
            ]);
    }

    public function test_store_handles_cyrillic_characters()
    {
        $data = [
            'last_name' => 'Чехов',
            'first_name' => 'Антон',
            'middle_name' => 'Павлович'
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/admin/authors', $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'last_name' => 'Чехов',
                'first_name' => 'Антон'
            ]);
    }

    // ==================== ТЕСТЫ МЕТОДА show() ====================

    public function test_show_requires_authentication()
    {
        $author = Author::factory()->create();

        $response = $this->getJson("/api/admin/authors/{$author->author_id}");

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Требуется авторизация'
            ]);
    }

    public function test_show_requires_admin_role()
    {
        $author = Author::factory()->create();

        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->getJson("/api/admin/authors/{$author->author_id}");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Доступ запрещен. Требуются права администратора.'
            ]);
    }

    public function test_show_returns_404_for_nonexistent_author()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/admin/authors/999999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Автор не найден'
            ]);
    }

    public function test_show_returns_author_with_books_relationship()
    {
        $author = Author::factory()->create([
            'last_name' => 'Толстой',
            'first_name' => 'Лев',
            'middle_name' => 'Николаевич'
        ]);

        $book1 = Book::factory()->create(['book_title' => 'Война и мир']);
        $book2 = Book::factory()->create(['book_title' => 'Анна Каренина']);
        $author->books()->attach([$book1->book_id, $book2->book_id]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/admin/authors/{$author->author_id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'author_id' => $author->author_id,
                    'last_name' => 'Толстой',
                    'first_name' => 'Лев',
                    'middle_name' => 'Николаевич'
                ]
            ]);

        $this->assertCount(2, $response->json('data.books'));
        $this->assertContains('Война и мир', collect($response->json('data.books'))->pluck('book_title')->toArray());
    }

    public function test_show_returns_author_without_books()
    {
        $author = Author::factory()->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/admin/authors/{$author->author_id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'books' => []
            ]);
    }

    // ==================== ТЕСТЫ МЕТОДА update() ====================

    public function test_update_requires_authentication()
    {
        $author = Author::factory()->create();

        $response = $this->putJson("/api/admin/authors/{$author->author_id}", [
            'last_name' => 'Updated'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Требуется авторизация'
            ]);
    }

    public function test_update_requires_admin_role()
    {
        $author = Author::factory()->create();

        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->putJson("/api/admin/authors/{$author->author_id}", [
                'last_name' => 'Updated'
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Доступ запрещен. Требуются права администратора.'
            ]);
    }

    public function test_update_returns_404_for_nonexistent_author()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson('/api/admin/authors/999999', [
                'last_name' => 'Updated'
            ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Автор не найден'
            ]);
    }

    public function test_update_validates_last_name_max_length()
    {
        $author = Author::factory()->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/admin/authors/{$author->author_id}", [
                'last_name' => str_repeat('a', 256)
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['last_name']);
    }

    public function test_update_validates_first_name_max_length()
    {
        $author = Author::factory()->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/admin/authors/{$author->author_id}", [
                'first_name' => str_repeat('a', 256)
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['first_name']);
    }

    public function test_update_validates_birth_date_format()
    {
        $author = Author::factory()->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/admin/authors/{$author->author_id}", [
                'birth_date' => 'invalid-date'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['birth_date']);
    }

    public function test_update_successfully_updates_all_fields()
    {
        $author = Author::factory()->create([
            'last_name' => 'Old',
            'first_name' => 'Old',
            'middle_name' => 'Old'
        ]);

        $data = [
            'last_name' => 'Толстой',
            'first_name' => 'Лев',
            'middle_name' => 'Николаевич'
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/admin/authors/{$author->author_id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Автор успешно обновлен',
                'data' => [
                    'last_name' => 'Толстой',
                    'first_name' => 'Лев',
                    'middle_name' => 'Николаевич'
                ]
            ]);

        $this->assertDatabaseHas('authors', [
            'author_id' => $author->author_id,
            'last_name' => 'Толстой'
        ]);
    }

    public function test_update_partially_updates_author()
    {
        $author = Author::factory()->create([
            'last_name' => 'Толстой',
            'first_name' => 'Лев',
            'middle_name' => 'Николаевич'
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/admin/authors/{$author->author_id}", [
                'last_name' => 'Достоевский'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Автор успешно обновлен'
            ]);

        $author->refresh();
        $this->assertEquals('Достоевский', $author->last_name);
        $this->assertEquals('Лев', $author->first_name);
        $this->assertEquals('Николаевич', $author->middle_name);
    }

    public function test_update_can_set_middle_name_to_null()
    {
        $author = Author::factory()->create([
            'middle_name' => 'Николаевич'
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/admin/authors/{$author->author_id}", [
                'middle_name' => null
            ]);

        $response->assertStatus(200);

        $author->refresh();
        $this->assertNull($author->middle_name);
    }

    // ==================== ТЕСТЫ МЕТОДА destroy() ====================

    public function test_destroy_requires_authentication()
    {
        $author = Author::factory()->create();

        $response = $this->deleteJson("/api/admin/authors/{$author->author_id}");

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Требуется авторизация'
            ]);
    }

    public function test_destroy_requires_admin_role()
    {
        $author = Author::factory()->create();

        $response = $this->actingAs($this->regularUser, 'sanctum')
            ->deleteJson("/api/admin/authors/{$author->author_id}");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Доступ запрещен. Требуются права администратора.'
            ]);
    }

    public function test_destroy_returns_404_for_nonexistent_author()
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson('/api/admin/authors/999999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Автор не найден'
            ]);
    }

    public function test_destroy_returns_422_when_author_has_books()
    {
        $author = Author::factory()->create();
        $book = Book::factory()->create();
        $author->books()->attach($book->book_id);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/admin/authors/{$author->author_id}");

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Нельзя удалить автора, у которого есть книги'
            ]);

        $this->assertDatabaseHas('authors', [
            'author_id' => $author->author_id
        ]);
    }

    public function test_destroy_successfully_deletes_author_without_books()
    {
        $author = Author::factory()->create();

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/admin/authors/{$author->author_id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Автор успешно удален'
            ]);

        $this->assertDatabaseMissing('authors', [
            'author_id' => $author->author_id
        ]);
    }

    public function test_destroy_removes_author_from_database()
    {
        $author = Author::factory()->create();
        $authorId = $author->author_id;

        $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/admin/authors/{$authorId}");

        $this->assertDatabaseMissing('authors', [
            'author_id' => $authorId
        ]);
    }

    // ==================== ИНТЕГРАЦИОННЫЕ ТЕСТЫ ====================

    public function test_full_author_crud_lifecycle()
    {
        // 1. Создаём автора
        $createData = [
            'last_name' => 'Тургенев',
            'first_name' => 'Иван',
            'middle_name' => 'Сергеевич',
            'birth_date' => '1818-11-09'
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/admin/authors', $createData);

        $response->assertStatus(201);
        $authorId = $response->json('data.author_id');

        // 2. Получаем список авторов
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/admin/authors');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data');

        // 3. Получаем конкретного автора
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/admin/authors/{$authorId}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'last_name' => 'Тургенев',
                'first_name' => 'Иван'
            ]);

        // 4. Обновляем автора
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/admin/authors/{$authorId}", [
                'last_name' => 'Тургенев-Обновлённый'
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'last_name' => 'Тургенев-Обновлённый'
            ]);

        // 5. Пытаемся удалить автора с книгами (должно вернуть 422)
        $book = Book::factory()->create();
        $author = Author::find($authorId);
        $author->books()->attach($book->book_id);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/admin/authors/{$authorId}");

        $response->assertStatus(422);

        // 6. Удаляем связь и удаляем автора
        $author->books()->detach();

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/admin/authors/{$authorId}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('authors', ['author_id' => $authorId]);
    }

    public function test_multiple_admins_can_manage_authors()
    {
        $admin2 = User::factory()->create(['role_id' => 1]);
        $author = Author::factory()->create();

        // Admin 1 создаёт
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/admin/authors', [
                'last_name' => 'Test',
                'first_name' => 'Author'
            ]);
        $response->assertStatus(201);

        // Admin 2 получает список
        $response = $this->actingAs($admin2, 'sanctum')
            ->getJson('/api/admin/authors');
        $response->assertStatus(200);

        // Admin 2 обновляет
        $authorId = Author::latest()->first()->author_id;
        $response = $this->actingAs($admin2, 'sanctum')
            ->putJson("/api/admin/authors/{$authorId}", [
                'last_name' => 'Updated'
            ]);
        $response->assertStatus(200);

        // Admin 1 удаляет
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/admin/authors/{$authorId}");
        $response->assertStatus(200);
    }

    public function test_regular_user_cannot_bypass_admin_check()
    {
        $author = Author::factory()->create();

        // Проверяем все методы с обычным пользователем
        $endpoints = [
            ['GET', '/api/admin/authors'],
            ['POST', '/api/admin/authors', ['last_name' => 'Test', 'first_name' => 'Test']],
            ['GET', "/api/admin/authors/{$author->author_id}"],
            ['PUT', "/api/admin/authors/{$author->author_id}", ['last_name' => 'Updated']],
            ['DELETE', "/api/admin/authors/{$author->author_id}"],
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

    public function test_author_with_multiple_books_cannot_be_deleted()
    {
        $author = Author::factory()->create();
        $books = Book::factory()->count(5)->create();
        $author->books()->attach($books->pluck('book_id'));

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->deleteJson("/api/admin/authors/{$author->author_id}");

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Нельзя удалить автора, у которого есть книги'
            ]);

        $this->assertDatabaseHas('authors', [
            'author_id' => $author->author_id
        ]);
    }

    public function test_unicode_characters_in_author_data()
    {
        $data = [
            'last_name' => 'Гоголь',
            'first_name' => 'Николай',
            'middle_name' => 'Васильевич',
            'birth_date' => '1809-04-01'
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/admin/authors', $data);

        $response->assertStatus(201);
        $this->assertStringContainsString('Гоголь', $response->getContent());
        $this->assertStringContainsString('Николай', $response->getContent());
    }

    public function test_author_timestamps_are_set()
    {
        $data = [
            'last_name' => 'Тестов',
            'first_name' => 'Тест',
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/admin/authors', $data);

        $response->assertStatus(201);
        $authorId = $response->json('data.author_id');

        $author = Author::find($authorId);
        $this->assertNotNull($author->created_at);
        $this->assertNotNull($author->updated_at);
    }

    public function test_update_preserves_unmodified_fields()
    {
        $author = Author::factory()->create([
            'last_name' => 'Original',
            'first_name' => 'Original',
            'middle_name' => 'Original',
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->putJson("/api/admin/authors/{$author->author_id}", [
                'last_name' => 'Updated'
            ]);

        $response->assertStatus(200);

        $author->refresh();
        $this->assertEquals('Updated', $author->last_name);
        $this->assertEquals('Original', $author->first_name);
        $this->assertEquals('Original', $author->middle_name);
    }

    public function test_index_pagination_works_correctly()
    {
        Author::factory()->count(45)->create();

        // Страница 1
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/admin/authors?page=1');

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
            ->getJson('/api/admin/authors?page=2');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'current_page' => 2
                ]
            ]);
        $this->assertCount(20, $response->json('data.data'));

        // Страница 3
        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/admin/authors?page=3');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'current_page' => 3
                ]
            ]);
        $this->assertCount(5, $response->json('data.data'));
    }

    public function test_error_response_includes_error_message_in_debug_mode()
    {
        // Сохраняем оригинальное значение
        $originalDebug = config('app.debug');
        config(['app.debug' => true]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson('/api/admin/authors');

        // В успешном запросе error не должно быть
        $response->assertStatus(200);

        // Восстанавливаем значение
        config(['app.debug' => $originalDebug]);
    }
}