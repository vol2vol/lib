<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\Role;
use App\Models\User;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_can_register(): void
    {
        Role::factory()->create(['role_id' => 2, 'role_name' => 'user']);

        $response = $this->post('/api/register', [
            'login' => 'test_user',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'user' => [
                'user_id',
                'login',
                'role_id',
            ],
        ]);
    }

    public function test_users_cannot_register_with_existing_login()
    {
        User::factory()->create(['login' => 'existing_user']);

        $response = $this->post('/api/register', [
            'login' => 'existing_user',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('login');
    }
}
