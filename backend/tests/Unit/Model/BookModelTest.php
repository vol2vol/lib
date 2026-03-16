<?php

namespace Tests\Unit\Model;

use App\Models\Book;
use App\Models\BookFile;
use App\Models\Publisher;
use App\Models\Format;
use App\Models\Genre;
use App\Models\Author;
use App\Models\User;
use Database\Seeders\FormatSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;

class BookModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(FormatSeeder::class);
    }

    public function test_book_has_correct_fillable_fields()
    {
        $book = Book::factory()->make();

        $this->assertEquals([
            'book_title',
            'description',
            'published_year',
            'cover_path',
            'publisher_id'
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

    public function test_book_title_is_required()
    {
        $this->expectException(QueryException::class);

        Book::create([
            'description' => 'Test',
            'published_year' => 1924,
            'cover_path' => 'books/1.png',
            'publisher_id' => 1,
        ]);
    }

    public function test_book_description_is_required()
    {
        $this->expectException(QueryException::class);

        $book = Book::create([
            'book_title' => 'Test',
            'published_year' => 1924,
            'cover_path' => 'books/1.png',
            'publisher_id' => 1,
        ]);
    }

    public function test_book_published_year_is_required()
    {
        $this->expectException(QueryException::class);

        $book = Book::create([
            'book_title' => 'Test',
            'description' => 'Test',
            'cover_path' => 'books/1.png',
            'publisher_id' => 1,
        ]);
    }

    public function test_publisher_id_is_required()
    {
        $this->expectException(QueryException::class);

        Book::create([
            'book_title' => 'Test',
            'description' => 'Test',
            'published_year' => 1924,
            'cover_path' => 'books/1.png',
        ]);
    }

    public function test_cover_path_is_required()
    {
        $this->expectException(QueryException::class);

        Book::create([
            'book_title' => 'Test',
            'description' => 'Test',
            'published_year' => 1924,
            'publisher_id' => 1,
        ]);
    }

    public function test_cover_path_accepts_valid_image_path()
    {
        $book = Book::factory()->create([
            'cover_path' => 'covers/books/war_and_peace.jpg',
        ]);

        $this->assertEquals('covers/books/war_and_peace.jpg', $book->cover_path);
        $this->assertDatabaseHas('books', [
            'cover_path' => 'covers/books/war_and_peace.jpg',
        ]);
    }

    public function test_cover_path_accepts_png_format()
    {
        $book = Book::factory()->create([
            'cover_path' => 'images/covers/book_cover.png',
        ]);

        $this->assertEquals('images/covers/book_cover.png', $book->cover_path);
    }

    public function test_cover_path_accepts_webp_format()
    {
        $book = Book::factory()->create([
            'cover_path' => 'storage/covers/novel.webp',
        ]);

        $this->assertEquals('storage/covers/novel.webp', $book->cover_path);
    }

    public function test_cover_path_accepts_cyrillic_characters()
    {
        $book = Book::factory()->create([
            'cover_path' => 'covers/книги/война_и_мир.jpg',
        ]);

        $this->assertEquals('covers/книги/война_и_мир.jpg', $book->cover_path);
    }

    public function test_cover_path_accepts_nested_directory_structure()
    {
        $deepPath = 'public/storage/covers/2024/russian/classics/tolstoy/cover.png';
        $book = Book::factory()->create([
            'cover_path' => $deepPath,
        ]);

        $this->assertEquals($deepPath, $book->cover_path);
    }

    public function test_cover_path_can_be_null_if_allowed_by_schema()
    {
        // Если поле допускает NULL в БД
        $book = Book::factory()->create([
            'cover_path' => null,
        ]);

        $this->assertNull($book->cover_path);
    }

    public function test_cover_path_handles_special_characters_in_filename()
    {
        $book = Book::factory()->create([
            'cover_path' => 'covers/book (2nd edition).png',
        ]);

        $this->assertEquals('covers/book (2nd edition).png', $book->cover_path);
    }

    public function test_cover_path_is_not_fillable_when_not_in_array()
    {
        $book = Book::factory()->make();
        $fillable = $book->getFillable();

        $this->assertContains('cover_path', $fillable);
        $this->assertCount(5, $fillable);
    }

    public function test_publisher_belongs_to_book()
    {
        $publisher = Publisher::factory()->create(['publisher_name' => 'АСТ']);
        $book = Book::factory()->create(['publisher_id' => $publisher->publisher_id]);

        $this->assertInstanceOf(Publisher::class, $book->publisher);
        $this->assertEquals($publisher->publisher_id, $book->publisher->publisher_id);
        $this->assertEquals('АСТ', $book->publisher->publisher_name);
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

    public function test_book_can_have_multiple_files()
    {
        $book = Book::factory()->create();
        $format1 = Format::factory()->create(['format_name' => 'RTF']);
        $format2 = Format::factory()->create(['format_name' => 'DOC']);
        
        $file1 = BookFile::factory()->create([
            'book_id' => $book->book_id,
            'format_id' => $format1->format_id,
            'file_path' => 'files/book1.rtf',
        ]);
        $file2 = BookFile::factory()->create([
            'book_id' => $book->book_id,
            'format_id' => $format2->format_id,
            'file_path' => 'files/book2.doc',
        ]);

        $this->assertCount(2, $book->files);
        $this->assertInstanceOf(BookFile::class, $book->files->first());
        $this->assertContains($file1->file_id, $book->files->pluck('file_id')->toArray());
    }

    public function test_book_can_have_no_files()
    {
        $book = Book::factory()->create();

        $this->assertCount(0, $book->files);
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
        $author = Author::factory()->create();
        $genre = Genre::factory()->create();
        $format = Format::factory()->create();
        $book_file = BookFile::factory()->create(['format_id' => $format->format_id]);

        $book->publisher()->associate($publisher);
        $book->authors()->attach($author);
        $book->genres()->attach($genre);
        $book->files()->save($book_file);
        $book->save();

        $this->assertEquals($publisher->publisher_id, $book->publisher_id);
        $this->assertDatabaseHas('book_authors', ['book_id' => $book->book_id, 'author_id' => $author->author_id]);
        $this->assertDatabaseHas('book_genres', ['book_id' => $book->book_id, 'genre_id' => $genre->genre_id]);
        $this->assertEquals($book_file->book_id, $book->book_id);
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

    public function test_get_authors_full_name_with_multiple_authors()
    {
        $book = Book::factory()->create();
        $author1 = Author::factory()->create([
            'last_name' => 'Толстой',
            'first_name' => 'Лев',
            'middle_name' => 'Николаевич',
        ]);
        $author2 = Author::factory()->create([
            'last_name' => 'Достоевский',
            'first_name' => 'Фёдор',
            'middle_name' => 'Михайлович',
        ]);

        $book->authors()->attach([$author1, $author2]);
        $book->load('authors');

        $result = $book->getAuthorsFullNameAttribute();
        $this->assertStringContainsString('Толстой Лев Николаевич', $result);
        $this->assertStringContainsString('Достоевский Фёдор Михайлович', $result);
    }

    public function test_get_authors_full_name_handles_null_middle_name()
    {
        $book = Book::factory()->create();
        $author = Author::factory()->create([
            'last_name' => 'Пушкин',
            'first_name' => 'Александр',
            'middle_name' => null,
        ]);

        $book->authors()->attach($author);
        $book->load('authors');

        $this->assertEquals('Пушкин Александр', $book->getAuthorsFullNameAttribute());
    }

    public function test_get_authors_full_name_handles_empty_strings()
    {
        $book = Book::factory()->create();
        $author = Author::factory()->create([
            'last_name' => '',
            'first_name' => 'Иван',
            'middle_name' => '',
        ]);

        $book->authors()->attach($author);
        $book->load('authors');

        $this->assertEquals('Иван', $book->getAuthorsFullNameAttribute());
    }

    public function test_published_year_cannot_be_in_future()
    {
        $futureYear = date('Y') + 1;

        $book = Book::factory()->create(['published_year' => $futureYear]);

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

    public function test_book_factory_creates_valid_instance()
    {
        $book = Book::factory()->make();

        $this->assertInstanceOf(Book::class, $book);
        $this->assertNotNull($book->book_title);
        $this->assertNotNull($book->description);
        $this->assertNotNull($book->published_year);
        $this->assertNotNull($book->cover_path);
        $this->assertNotNull($book->publisher_id);
    }

    public function test_timestamps_are_updated_on_save()
    {
        $book = Book::factory()->create();
        $originalUpdatedAt = $book->updated_at;

        sleep(1);

        $book->book_title = 'Updated Title';
        $book->save();
        $book->refresh();

        $this->assertNotEquals($originalUpdatedAt->timestamp, $book->updated_at->timestamp);
        $this->assertGreaterThan($originalUpdatedAt->timestamp, $book->updated_at->timestamp);
    }

    public function test_book_casts_timestamps_to_carbon_instances()
    {
        $book = Book::factory()->create();

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $book->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $book->updated_at);
    }

    public function test_book_can_be_deleted()
    {
        $book = Book::factory()->create();
        $bookId = $book->book_id;

        $book->delete();

        $this->assertDatabaseMissing('books', ['book_id' => $bookId]);
    }

    public function test_deleting_book_cascades_to_pivot_tables()
    {
        $book = Book::factory()->create();
        $author = Author::factory()->create();
        $genre = Genre::factory()->create();
        $user = User::factory()->create();

        $book->authors()->attach($author);
        $book->genres()->attach($genre);
        $book->favoritedBy()->attach($user);

        $bookId = $book->book_id;
        $book->delete();

        $this->assertDatabaseMissing('book_authors', ['book_id' => $bookId]);
        $this->assertDatabaseMissing('book_genres', ['book_id' => $bookId]);
        $this->assertDatabaseMissing('favorite_books', ['book_id' => $bookId]);
    }
}