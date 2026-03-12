<?php

namespace Tests\Feature\Admin;

use App\Models\Genre;
use App\Models\Role;
use App\Models\User;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Создаём роли
        $adminRole = Role::factory()->create(['role_name' => 'admin', 'role_id' => 1]);
        $userRole = Role::factory()->create(['role_name' => 'user', 'role_id' => 2]);

        // Создаём пользователей
        $this->admin = User::factory()->create([
            'login' => 'admin',
            'password' => bcrypt('admin123'),
            'role_id' => $adminRole->role_id,
        ]);

        $this->regularUser = User::factory()->create([
            'login' => 'user',
            'password' => bcrypt('user123'),
            'role_id' => $userRole->role_id,
        ]);
    }

    // ==================== ТЕСТЫ АВТОРИЗАЦИИ ====================

    public function test_guest_cannot_access_genres_index()
    {
        $response = $this->getJson('/api/admin/genres');

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Требуется авторизация',
        ]);
    }

    public function test_regular_user_cannot_access_genres_index()
    {
        $response = $this->actingAs($this->regularUser)
            ->getJson('/api/admin/genres');

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Доступ запрещен. Требуются права администратора.',
        ]);
    }

    public function test_admin_can_access_genres_index()
    {
        Genre::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/genres');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonCount(5, 'data.data');
    }

    // ==================== ТЕСТЫ СОЗДАНИЯ (STORE) ====================

    public function test_guest_cannot_create_genre()
    {
        $response = $this->postJson('/api/admin/genres', [
            'genre_name' => 'Фантастика',
        ]);

        $response->assertStatus(401);
    }

    public function test_regular_user_cannot_create_genre()
    {
        $response = $this->actingAs($this->regularUser)
            ->postJson('/api/admin/genres', [
                'genre_name' => 'Фантастика',
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_create_genre_with_valid_data()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/genres', [
                'genre_name' => 'Фантастика',
            ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'message' => 'Жанр успешно создан',
        ]);

        $this->assertDatabaseHas('genres', [
            'genre_name' => 'Фантастика',
        ]);
    }

    public function test_cannot_create_genre_without_name()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/genres', []);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Ошибка валидации',
        ]);
    }

    public function test_cannot_create_genre_with_duplicate_name()
    {
        Genre::factory()->create(['genre_name' => 'Фантастика']);

        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/genres', [
                'genre_name' => 'Фантастика',
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Ошибка валидации',
        ]);
    }

    public function test_can_create_genre_with_russian_letters()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/genres', [
                'genre_name' => 'Научная фантастика',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('genres', [
            'genre_name' => 'Научная фантастика',
        ]);
    }

    // ==================== ТЕСТЫ ПРОСМОТРА (SHOW) ====================

    public function test_admin_can_view_genre_details()
    {
        $genre = Genre::factory()->create([
            'genre_name' => 'Детектив',
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson("/api/admin/genres/{$genre->genre_id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'genre_id' => $genre->genre_id,
                'genre_name' => 'Детектив',
            ],
        ]);
    }

    public function test_returns_404_for_nonexistent_genre()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/genres/999999');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Жанр не найден',
        ]);
    }

    // ==================== ТЕСТЫ ОБНОВЛЕНИЯ (UPDATE) ====================

    public function test_admin_can_update_genre_name()
    {
        $genre = Genre::factory()->create([
            'genre_name' => 'Старый жанр',
        ]);

        $response = $this->actingAs($this->admin)
            ->putJson("/api/admin/genres/{$genre->genre_id}", [
                'genre_name' => 'Новый жанр',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Жанр успешно обновлен',
        ]);

        $this->assertDatabaseHas('genres', [
            'genre_id' => $genre->genre_id,
            'genre_name' => 'Новый жанр',
        ]);
    }

    public function test_cannot_update_nonexistent_genre()
    {
        $response = $this->actingAs($this->admin)
            ->putJson('/api/admin/genres/999999', [
                'genre_name' => 'Тест',
            ]);

        $response->assertStatus(404);
    }

    public function test_cannot_update_genre_to_duplicate_name()
    {
        Genre::factory()->create(['genre_name' => 'Фантастика']);
        $genre2 = Genre::factory()->create(['genre_name' => 'Детектив']);

        $response = $this->actingAs($this->admin)
            ->putJson("/api/admin/genres/{$genre2->genre_id}", [
                'genre_name' => 'Фантастика', // дубликат
            ]);

        $response->assertStatus(422);
    }

    // ==================== ТЕСТЫ УДАЛЕНИЯ (DESTROY) ====================

    public function test_admin_can_delete_genre_without_books()
    {
        $genre = Genre::factory()->create([
            'genre_name' => 'Временный жанр',
        ]);

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/admin/genres/{$genre->genre_id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Жанр успешно удален',
        ]);

        $this->assertDatabaseMissing('genres', [
            'genre_id' => $genre->genre_id,
        ]);
    }

    public function test_cannot_delete_genre_with_books()
    {
        $genre = Genre::factory()->create();
        Book::factory()->withGenre($genre)->create();

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/admin/genres/{$genre->genre_id}");

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Нельзя удалить жанр, в котором есть книги',
        ]);

        // Жанр должен остаться в БД
        $this->assertDatabaseHas('genres', [
            'genre_id' => $genre->genre_id,
        ]);
    }

    public function test_cannot_delete_nonexistent_genre()
    {
        $response = $this->actingAs($this->admin)
            ->deleteJson('/api/admin/genres/999999');

        $response->assertStatus(404);
    }

    // ==================== ТЕСТЫ ПАГИНАЦИИ ====================

    public function test_genres_index_returns_paginated_response()
    {
        Genre::factory()->count(25)->create();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/genres');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'data',
                'current_page',
                'last_page',
                'per_page',
                'total',
            ],
        ]);

        // Первая страница должна содержать 20 записей
        $this->assertCount(20, $response->json('data.data'));
        $this->assertEquals(2, $response->json('data.last_page'));
    }

    public function test_genres_index_includes_books_count()
    {
        $genre1 = Genre::factory()->create();
        $genre2 = Genre::factory()->create();

        Book::factory()->count(3)->withGenre($genre1)->create();
        Book::factory()->count(5)->withGenre($genre2)->create();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/genres');

        $genres = $response->json('data.data');
        $genre1Data = collect($genres)->firstWhere('genre_id', $genre1->genre_id);
        $genre2Data = collect($genres)->firstWhere('genre_id', $genre2->genre_id);

        $this->assertEquals(3, $genre1Data['books_count']);
        $this->assertEquals(5, $genre2Data['books_count']);
    }

    // ==================== ТЕСТЫ ОБРАБОТКИ ОШИБОК ====================

    public function test_handles_database_exception_gracefully()
    {
        $this->withoutExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/genres', [
                'genre_name' => str_repeat('x', 300), // Слишком длинное имя
            ]);

        // Ожидаем ошибку 422 или 500 в зависимости от СУБД
        $this->assertTrue(
            in_array($response->status(), [422, 500]),
            'Expected status 422 or 500, got ' . $response->status()
        );
    }

    // ==================== ДОПОЛНИТЕЛЬНЫЕ ТЕСТЫ ====================

    public function test_genre_name_can_contain_special_characters()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/genres', [
                'genre_name' => 'Боевик-триллер',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('genres', [
            'genre_name' => 'Боевик-триллер',
        ]);
    }

    public function test_genre_name_can_contain_multiple_words()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/genres', [
                'genre_name' => 'Научная литература',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('genres', [
            'genre_name' => 'Научная литература',
        ]);
    }
}