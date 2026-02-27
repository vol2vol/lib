<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        // Создаём роль "user" если её нет
        $roleId = Role::where('role_name', 'user')->value('role_id') 
            ?? Role::factory()->create(['role_name' => 'user'])->role_id;

        return [
            'login' => $this->faker->unique()->userName(),
            'password' => 'password123', // Хешированный пароль
            'role_id' => $roleId,
        ];
    }

    public function admin(): static
    {
        return $this->state(function (array $attributes) {
            $adminRoleId = Role::where('role_name', 'admin')->value('role_id') 
                ?? Role::factory()->create(['role_name' => 'admin'])->role_id;

            return [
                'role_id' => $adminRoleId,
            ];
        });
    }
}