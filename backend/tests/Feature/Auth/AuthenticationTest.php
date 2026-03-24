<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/api/login', [
            'login' => $user->login,
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'user' => ['user_id', 'login', 'role_id'],
        ]);
        $this->assertNotEmpty($response->json('access_token'));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/api/login', [
            'login' => $user->login,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Неверный логин или пароль'
        ]);
    }

    public function test_users_can_not_authenticate_with_invalid_login(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/api/login', [
            'login' => 'nonexistent-login',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Неверный логин или пароль'
        ]);
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $response = $this->post('/api/logout');

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Вы успешно вышли из системы'
        ]);
    }

}
