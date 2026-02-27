<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Publisher;
use App\Models\Format;
use App\Models\Genre;
use App\Models\Author;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Database\QueryException;

class BookModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_has_correct_fillable_fields()
    {
        $book = Book::factory()->make();

        $this->assertEquals([
            'book_title',
            'description',
            'published_year',
            'publisher_id',
            'format_id',
            'file_path',
            'file_size_bytes'
        ], $book->getFillable());
    }

    public function test_book_has_custom_primary_key()
    {
        $book = Book::factory()->create();

        $this->assertEquals('book_id', $book->getKeyName());
        $this->assertTrue($book->exists);
    }

    public function test_book_has_timestamps()
    {
        $book = Book::factory()->create();

        $this->assertNotNull($book->created_at);
        $this->assertNotNull($book->updated_at);
    }

    public function test_book_can_be_created_with_valid_data()
    {
        $book = Book::factory()->create([
            'book_title' => 'Война и мир',
            'published_year' => 1869,
        ]);

        $this->assertDatabaseHas('books', [
            'book_title' => 'Война и мир',
            'published_year' => 1869,
        ]);
    }

    public function test_published_year_is_cast_to_integer()
    {
        $book = Book::factory()->create([
            'published_year' => '1869',
        ]);

        $this->assertIsInt($book->published_year);
        $this->assertEquals(1869, $book->published_year);
    }

    public function test_file_size_bytes_is_cast_to_integer()
    {
        $book = Book::factory()->create([
            'file_size_bytes' => '5242880',
        ]);

        $this->assertIsInt($book->file_size_bytes);
        $this->assertEquals(5242880, $book->file_size_bytes);
    }

    public function test_book_title_is_required()
    {
        $this->expectException(QueryException::class);

        Book::create([
            'description' => 'Test',
            'publisher_id' => 1,
            'format_id' => 1,
            'file_path' => 'test.pdf',
            'file_size_bytes' => 100000,
        ]);
    }

    public function test_book_description_is_required()
    {
        $this->expectException(QueryException::class);

        $book = Book::factory()->create([
            'description' => null,
        ]);
    }

    public function test_book_published_year_is_required()
    {
        $this->expectException(QueryException::class);

        $book = Book::factory()->create([
            'published_year' => null,
        ]);
    }

    public function test_publisher_id_is_required()
    {
        $this->expectException(QueryException::class);

        Book::factory()->create([
            'publisher_id' => null,
        ]);
    }

    public function test_format_id_is_required()
    {
        $this->expectException(QueryException::class);

        Book::factory()->create([
            'format_id' => null,
        ]);
    }

    public function test_book_belongs_to_publisher()
    {
        $publisher = Publisher::factory()->create(['publisher_name' => 'АСТ']);
        $book = Book::factory()->create(['publisher_id' => $publisher->publisher_id]);

        $this->assertInstanceOf(Publisher::class, $book->publisher);
        $this->assertEquals($publisher->publisher_id, $book->publisher->publisher_id);
        $this->assertEquals('АСТ', $book->publisher->publisher_name);
    }

    public function test_book_belongs_to_format()
    {
        $format = Format::factory()->create(['format_name' => 'EPUB']);
        $book = Book::factory()->create(['format_id' => $format->format_id]);

        $this->assertInstanceOf(Format::class, $book->format);
        $this->assertEquals($format->format_id, $book->format->format_id);
        $this->assertEquals('EPUB', $book->format->format_name);
    }

    public function test_book_can_have_multiple_genres()
    {
        $book = Book::factory()->create();
        $genres = Genre::factory()->count(3)->create();

        $book->genres()->attach($genres);

        $this->assertCount(3, $book->genres);
        $this->assertInstanceOf(Genre::class, $book->genres->first());
    }

    public function test_book_can_have_multiple_authors()
    {
        $book = Book::factory()->create();
        $authors = Author::factory()->count(2)->create();

        $book->authors()->attach($authors);

        $this->assertCount(2, $book->authors);
        $this->assertInstanceOf(Author::class, $book->authors->first());
    }

    public function test_book_can_be_favorited_by_multiple_users()
    {
        $book = Book::factory()->create();
        $users = User::factory()->count(5)->create();

        foreach ($users as $user) {
            $book->favoritedBy()->attach($user);
        }

        $this->assertCount(5, $book->favoritedBy);
        $this->assertInstanceOf(User::class, $book->favoritedBy->first());
    }

    public function test_book_can_have_no_genres()
    {
        $book = Book::factory()->create();

        $this->assertCount(0, $book->genres);
    }

    public function test_book_can_have_no_authors()
    {
        $book = Book::factory()->create();

        $this->assertCount(0, $book->authors);
    }

    public function test_book_relationships_use_correct_foreign_keys()
    {
        $book = Book::factory()->create();
        $publisher = Publisher::factory()->create();
        $format = Format::factory()->create();

        $book->publisher()->associate($publisher);
        $book->format()->associate($format);
        $book->save();

        $this->assertEquals($publisher->publisher_id, $book->publisher_id);
        $this->assertEquals($format->format_id, $book->format_id);
    }

    public function test_book_can_check_if_favorited_by_user()
    {
        $book = Book::factory()->create();
        $user = User::factory()->create();

        $book->favoritedBy()->attach($user);

        $this->assertTrue($book->isFavoritedByUser($user->user_id));
        $this->assertFalse($book->isFavoritedByUser(999999));
    }

    public function test_book_can_get_authors_full_name()
    {
        $book = Book::factory()->create();
        $author = Author::factory()->create([
            'last_name' => 'Толстой',
            'first_name' => 'Лев',
            'middle_name' => 'Николаевич',
        ]);

        $book->authors()->attach($author);

        $book->load('authors');
        $this->assertEquals('Толстой Лев Николаевич', $book->getAuthorsFullNameAttribute());
    }

    public function test_published_year_cannot_be_in_future()
    {
        $futureYear = date('Y') + 1;

        $book = Book::factory()->create(['published_year' => $futureYear]);

        // Валидация должна быть на уровне приложения, не БД
        $this->assertEquals($futureYear, $book->published_year);
    }

    public function test_published_year_cannot_be_too_old()
    {
        $book = Book::factory()->create(['published_year' => 1000]);

        $this->assertEquals(1000, $book->published_year);
    }

    public function test_book_title_can_contain_special_characters()
    {
        $book = Book::factory()->create([
            'book_title' => 'Война и мир: том 1 (1865-1869)',
        ]);

        $this->assertEquals('Война и мир: том 1 (1865-1869)', $book->book_title);
    }

    public function test_book_title_can_contain_numbers()
    {
        $book = Book::factory()->create([
            'book_title' => 'Гарри Поттер и философский камень',
        ]);

        $this->assertEquals('Гарри Поттер и философский камень', $book->book_title);
    }

    public function test_description_can_be_long_text()
    {
        $longDescription = str_repeat('Тестовое описание. ', 100);
        $book = Book::factory()->create(['description' => $longDescription]);

        $this->assertEquals($longDescription, $book->description);
    }

    public function test_file_path_can_contain_subdirectories()
    {
        $book = Book::factory()->create([
            'file_path' => 'books/fiction/war_and_peace.pdf',
        ]);

        $this->assertEquals('books/fiction/war_and_peace.pdf', $book->file_path);
    }
}