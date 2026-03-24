<?php

namespace Tests\Unit\Model;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_author_has_correct_primary_key()
    {
        $author = Author::factory()->create();

        $this->assertEquals('author_id', $author->getKeyName());
        $this->assertTrue($author->getKeyType() === 'int');
    }

    public function test_author_has_fillable_attributes()
    {
        $author = Author::factory()->create([
            'last_name' => 'Толстой',
            'first_name' => 'Лев',
            'middle_name' => 'Николаевич',
        ]);

        $this->assertEquals('Толстой', $author->last_name);
        $this->assertEquals('Лев', $author->first_name);
        $this->assertEquals('Николаевич', $author->middle_name);
    }

    public function test_author_can_have_null_middle_name()
    {
        $author = Author::factory()->withoutMiddleName()->create();

        $this->assertNull($author->middle_name);
    }

    public function test_author_has_many_to_many_relationship_with_books()
    {
        $author = Author::factory()->create();
        $book1 = Book::factory()->create();
        $book2 = Book::factory()->create();

        // Прикрепляем книги к автору через промежуточную таблицу
        $author->books()->attach([$book1->book_id, $book2->book_id]);

        // Перегружаем связь
        $author->load('books');

        $this->assertCount(2, $author->books);
        $this->assertInstanceOf(Book::class, $author->books->first());
        $this->assertTrue($author->books->contains($book1));
        $this->assertTrue($author->books->contains($book2));
    }

    public function test_author_can_be_created_with_full_name()
    {
        $author = Author::factory()
            ->withFullName('Александр', 'Сергеевич', 'Пушкин')
            ->create();

        $this->assertEquals('Пушкин', $author->last_name);
        $this->assertEquals('Александр', $author->first_name);
        $this->assertEquals('Сергеевич', $author->middle_name);
    }

    public function test_timestamps_are_enabled()
    {
        $author = Author::factory()->create();

        $this->assertNotNull($author->created_at);
        $this->assertNotNull($author->updated_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $author->created_at);
    }

    public function test_author_can_be_updated()
    {
        $author = Author::factory()->create([
            'last_name' => 'Толстой',
        ]);

        $author->update([
            'last_name' => 'Достоевский',
        ]);

        $this->assertEquals('Достоевский', $author->fresh()->last_name);
    }

    public function test_author_can_be_deleted()
    {
        $author = Author::factory()->create();

        $authorId = $author->author_id;
        $author->delete();

        $this->assertNull(Author::find($authorId));
    }
}