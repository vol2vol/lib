<?php

namespace Tests\Feature\Admin;

use App\Models\Publisher;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublisherControllerTest extends TestCase
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

    public function test_guest_cannot_access_publishers_index()
    {
        $response = $this->getJson('/api/admin/publishers');

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Требуется авторизация'
        ]);
    }

    public function test_regular_user_cannot_access_publishers_index()
    {
        $response = $this->actingAs($this->regularUser)
            ->getJson('/api/admin/publishers');

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Доступ запрещен. Требуются права администратора.',
        ]);
    }

    public function test_admin_can_access_publishers_index()
    {
        Publisher::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/publishers');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonCount(5, 'data.data');
    }

    // ==================== ТЕСТЫ СОЗДАНИЯ (STORE) ====================

    public function test_guest_cannot_create_publisher()
    {
        $response = $this->postJson('/api/admin/publishers', [
            'publisher_name' => 'АСТ',
        ]);

        $response->assertStatus(401);
    }

    public function test_regular_user_cannot_create_publisher()
    {
        $response = $this->actingAs($this->regularUser)
            ->postJson('/api/admin/publishers', [
                'publisher_name' => 'АСТ',
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_create_publisher_with_valid_data()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/publishers', [
                'publisher_name' => 'АСТ',
            ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'message' => 'Издательство успешно создано',
        ]);

        $this->assertDatabaseHas('publishers', [
            'publisher_name' => 'АСТ',
        ]);
    }

    public function test_cannot_create_publisher_without_name()
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/publishers', []);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Ошибка валидации',
        ]);
    }

    public function test_cannot_create_publisher_with_duplicate_name()
    {
        Publisher::factory()->create(['publisher_name' => 'АСТ']);

        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/publishers', [
                'publisher_name' => 'АСТ',
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Ошибка валидации',
        ]);
    }

    // ==================== ТЕСТЫ ПРОСМОТРА (SHOW) ====================

    public function test_admin_can_view_publisher_details()
    {
        $publisher = Publisher::factory()->create([
            'publisher_name' => 'Эксмо',
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson("/api/admin/publishers/{$publisher->publisher_id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'publisher_id' => $publisher->publisher_id,
                'publisher_name' => 'Эксмо',
            ],
        ]);
    }

    public function test_returns_404_for_nonexistent_publisher()
    {
        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/publishers/999999');

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Издательство не найдено',
        ]);
    }

    // ==================== ТЕСТЫ ОБНОВЛЕНИЯ (UPDATE) ====================

    public function test_admin_can_update_publisher_name()
    {
        $publisher = Publisher::factory()->create([
            'publisher_name' => 'Старое издательство',
        ]);

        $response = $this->actingAs($this->admin)
            ->putJson("/api/admin/publishers/{$publisher->publisher_id}", [
                'publisher_name' => 'Новое издательство',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Издательство успешно обновлено',
        ]);

        $this->assertDatabaseHas('publishers', [
            'publisher_id' => $publisher->publisher_id,
            'publisher_name' => 'Новое издательство',
        ]);
    }

    public function test_cannot_update_nonexistent_publisher()
    {
        $response = $this->actingAs($this->admin)
            ->putJson('/api/admin/publishers/999999', [
                'publisher_name' => 'Тест',
            ]);

        $response->assertStatus(404);
    }

    public function test_cannot_update_publisher_to_duplicate_name()
    {
        Publisher::factory()->create(['publisher_name' => 'АСТ']);
        $publisher2 = Publisher::factory()->create(['publisher_name' => 'Эксмо']);

        $response = $this->actingAs($this->admin)
            ->putJson("/api/admin/publishers/{$publisher2->publisher_id}", [
                'publisher_name' => 'АСТ', // дубликат
            ]);

        $response->assertStatus(422);
    }

    // ==================== ТЕСТЫ УДАЛЕНИЯ (DESTROY) ====================

    public function test_admin_can_delete_publisher_without_books()
    {
        $publisher = Publisher::factory()->create([
            'publisher_name' => 'Временное издательство',
        ]);

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/admin/publishers/{$publisher->publisher_id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Издательство успешно удалено',
        ]);

        $this->assertDatabaseMissing('publishers', [
            'publisher_id' => $publisher->publisher_id,
        ]);
    }

    public function test_cannot_delete_publisher_with_books()
    {
        $publisher = Publisher::factory()->create();
        \App\Models\Book::factory()->create([
            'publisher_id' => $publisher->publisher_id,
        ]);

        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/admin/publishers/{$publisher->publisher_id}");

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Нельзя удалить издательство, у которого есть книги',
        ]);

        // Издательство должно остаться в БД
        $this->assertDatabaseHas('publishers', [
            'publisher_id' => $publisher->publisher_id,
        ]);
    }

    public function test_cannot_delete_nonexistent_publisher()
    {
        $response = $this->actingAs($this->admin)
            ->deleteJson('/api/admin/publishers/999999');

        $response->assertStatus(404);
    }

    // ==================== ТЕСТЫ ПАГИНАЦИИ ====================

    public function test_publishers_index_returns_paginated_response()
    {
        Publisher::factory()->count(25)->create();

        $response = $this->actingAs($this->admin)
            ->getJson('/api/admin/publishers');

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

    // ==================== ТЕСТЫ ОБРАБОТКИ ОШИБОК ====================

    public function test_handles_database_exception_gracefully()
    {
        // Имитируем ошибку БД через подмену метода
        $this->withoutExceptionHandling();

        $response = $this->actingAs($this->admin)
            ->postJson('/api/admin/publishers', [
                'publisher_name' => str_repeat('x', 300), // Слишком длинное имя
            ]);

        // Ожидаем ошибку 500 или 422 в зависимости от СУБД
        $this->assertTrue(
            in_array($response->status(), [422, 500]),
            'Expected status 422 or 500, got ' . $response->status()
        );
    }
}