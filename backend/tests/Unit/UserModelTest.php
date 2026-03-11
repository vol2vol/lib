<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\User;
use App\Models\Book;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_correct_fillable_fields()
    {
        $user = User::factory()->make();

        $this->assertEquals(['login', 'password', 'role_id'], $user->getFillable());
    }

    public function test_user_has_custom_primary_key()
    {
        $user = User::factory()->create();

        $this->assertEquals('user_id', $user->getKeyName());
        $this->assertTrue($user->exists);
    }

    public function test_user_has_timestamps()
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->created_at);
        $this->assertNotNull($user->updated_at);
    }

    public function test_password_is_hidden_in_array()
    {
        $user = User::factory()->create([
            'password' => 'secret123',
        ]);

        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
    }

    public function test_password_is_hashed_on_creation()
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        $this->assertNotEquals('password123', $user->password);
        $this->assertTrue(password_verify('password123', $user->password));
    }

    public function test_user_belongs_to_role()
    {
        $role = Role::factory()->create(['role_name' => 'user']);
        $user = User::factory()->create(['role_id' => $role->role_id]);

        $this->assertInstanceOf(Role::class, $user->role);
        $this->assertEquals($role->role_id, $user->role->role_id);
    }

    public function test_role_relationship_uses_correct_foreign_key()
    {
        $role = Role::factory()->create();
        $user = User::factory()->create(['role_id' => $role->role_id]);

        $this->assertEquals($role->role_id, $user->role_id);
    }

    public function test_user_is_created_with_default_role()
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->role_id);
        $this->assertDatabaseHas('users', [
            'user_id' => $user->user_id,
            'role_id' => $user->role_id,
        ]);
        $this->assertEquals('user', $user->role->role_name);
    }

    public function test_user_can_be_admin()
    {
        $adminRole = Role::factory()->create(['role_name' => 'admin']);
        $user = User::factory()->create(['role_id' => $adminRole->role_id]);

        $this->assertEquals('admin', $user->role->role_name);
    }

    public function test_user_can_be_regular_user()
    {
        $userRole = Role::factory()->create(['role_name' => 'user']);
        $user = User::factory()->create(['role_id' => $userRole->role_id]);

        $this->assertEquals('user', $user->role->role_name);
    }

    public function test_user_can_have_favorite_books()
    {
        $user = User::factory()->create();
        $books = Book::factory()->count(3)->create();

        $user->favoriteBooks()->attach($books);

        $this->assertCount(3, $user->favoriteBooks);
        $this->assertInstanceOf(Book::class, $user->favoriteBooks->first());
    }

    public function test_user_favorite_books_relationship_uses_correct_pivot_table()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $user->favoriteBooks()->attach($book);

        $this->assertDatabaseHas('favorite_books', [
            'user_id' => $user->user_id,
            'book_id' => $book->book_id,
        ]);
    }

    public function test_user_can_add_book_to_favorites()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $user->favoriteBooks()->attach($book);

        $this->assertTrue($user->favoriteBooks->contains($book));
    }

    public function test_user_can_remove_book_from_favorites()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $user->favoriteBooks()->attach($book);
        $this->assertCount(1, $user->favoriteBooks);

        $user->favoriteBooks()->detach($book);
        $user->load('favoriteBooks');
        $this->assertCount(0, $user->favoriteBooks);
    }

    public function test_user_can_have_no_favorite_books()
    {
        $user = User::factory()->create();

        $this->assertCount(0, $user->favoriteBooks);
    }

    public function test_password_is_hashed_before_saving()
    {
        $user = User::factory()->make([
            'password' => 'plain_password',
        ]);

        $this->assertNotEquals('plain_password', $user->password);
        $this->assertTrue(Hash::check('plain_password', $user->password));
    }

    public function test_user_can_be_authenticated()
    {
        $user = User::factory()->create([
            'login' => 'testuser',
            'password' => 'password123',
        ]);
        
        $authenticated = Auth::attempt([
            'login' => 'testuser',
            'password' => 'password123',
        ]);

        $this->assertTrue($authenticated);
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_has_api_tokens_trait()
    {
        $user = User::factory()->create();

        $this->assertTrue(method_exists($user, 'createToken'));
    }

    public function test_user_can_create_api_token()
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token');

        $this->assertNotNull($token->plainTextToken);
        $this->assertEquals('test-token', $token->accessToken->name);
    }

    public function test_login_is_required()
    {
        $this->expectException(QueryException::class);

        User::create([
            'password' => bcrypt('password123'),
            'role_id' => 1,
        ]);
    }

    public function test_login_must_be_unique()
    {
        User::factory()->create(['login' => 'john_doe']);

        $this->expectException(QueryException::class);
        User::factory()->create(['login' => 'john_doe']);
    }

    public function test_password_is_required()
    {
        $this->expectException(QueryException::class);

        User::create([
            'login' => 'testuser',
            'role_id' => 1,
        ]);
    }

    public function test_role_id_is_required()
    {
        $this->expectException(QueryException::class);

        User::create([
            'login' => 'testuser',
            'password' => bcrypt('password123'),
        ]);
    }

    public function test_login_can_contain_underscores_and_numbers()
    {
        $user = User::factory()->create([
            'login' => 'user_123',
        ]);

        $this->assertEquals('user_123', $user->login);
    }
}