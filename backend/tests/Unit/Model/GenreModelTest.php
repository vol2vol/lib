<?php

namespace Tests\Unit;

use App\Models\Genre;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Database\QueryException;

class GenreModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_genre_has_correct_fillable_fields()
    {
        $genre = Genre::factory()->make();

        $this->assertEquals(['genre_name'], $genre->getFillable());
    }

    public function test_genre_has_custom_primary_key()
    {
        $genre = Genre::factory()->create();

        $this->assertEquals('genre_id', $genre->getKeyName());
        $this->assertTrue($genre->exists);
    }

    public function test_genre_has_timestamps()
    {
        $genre = Genre::factory()->create();

        $this->assertNotNull($genre->created_at);
        $this->assertNotNull($genre->updated_at);
    }

    public function test_genre_can_be_created_with_valid_data()
    {
        $genre = Genre::factory()->create([
            'genre_name' => 'Фантастика',
        ]);

        $this->assertDatabaseHas('genres', [
            'genre_name' => 'Фантастика',
        ]);
    }

    public function test_genre_has_many_books()
    {
        $genre = Genre::factory()->create();
        $books = Book::factory()->count(3)->create();
        
        // Прикрепляем книги к жанру через pivot-таблицу
        $genre->books()->attach($books);

        // Перезагружаем связь
        $genre->load('books');

        $this->assertCount(3, $genre->books);
        $this->assertInstanceOf(Book::class, $genre->books->first());
    }

    public function test_genre_books_relationship_uses_correct_foreign_key()
    {
        $genre = Genre::factory()->create();
        $book = Book::factory()->create();
        
        // Прикрепляем книгу к жанру
        $book->genres()->attach($genre);

        // Перезагружаем связь
        $book->load('genres');

        $this->assertTrue($book->genres->contains($genre));
        $this->assertEquals($genre->genre_id, $book->genres->first()->genre_id);
    }

    public function test_genre_can_have_no_books()
    {
        $genre = Genre::factory()->create();

        $this->assertCount(0, $genre->books);
    }

    public function test_genre_books_relationship_has_correct_keys()
    {
        $genre = Genre::factory()->create();
        
        // Проверяем, что отношение использует правильные ключи
        $relation = $genre->books();
        
        $this->assertEquals('genre_id', $relation->getForeignPivotKeyName());
        $this->assertEquals('book_id', $relation->getRelatedPivotKeyName());
    }

    public function test_genre_name_is_required()
    {
        $this->expectException(QueryException::class);

        Genre::create([]);
    }

    public function test_genre_name_must_be_unique()
    {
        Genre::factory()->create(['genre_name' => 'Фантастика']);

        $this->expectException(QueryException::class);
        Genre::factory()->create(['genre_name' => 'Фантастика']);
    }

    public function test_genre_name_can_contain_russian_letters()
    {
        $genre = Genre::factory()->create([
            'genre_name' => 'Детектив',
        ]);

        $this->assertEquals('Детектив', $genre->genre_name);
    }

    public function test_genre_name_can_contain_multiple_words()
    {
        $genre = Genre::factory()->create([
            'genre_name' => 'Научная фантастика',
        ]);

        $this->assertEquals('Научная фантастика', $genre->genre_name);
    }

    public function test_genre_name_can_contain_hyphens()
    {
        $genre = Genre::factory()->create([
            'genre_name' => 'Боевик-триллер',
        ]);

        $this->assertEquals('Боевик-триллер', $genre->genre_name);
    }
}