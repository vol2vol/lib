<?php

namespace Tests\Unit;

use App\Models\Publisher;
use App\Models\Book;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublisherModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_publisher_has_correct_fillable_fields()
    {
        $publisher = Publisher::factory()->make();

        $this->assertEquals(['publisher_name'], $publisher->getFillable());
    }

    public function test_publisher_has_custom_primary_key()
    {
        $publisher = Publisher::factory()->create();

        $this->assertEquals('publisher_id', $publisher->getKeyName());
        $this->assertTrue($publisher->exists);
    }

    public function test_publisher_has_timestamps()
    {
        $publisher = Publisher::factory()->create();

        $this->assertNotNull($publisher->created_at);
        $this->assertNotNull($publisher->updated_at);
    }

    public function test_publisher_can_be_created_with_valid_data()
    {
        $publisher = Publisher::factory()->create([
            'publisher_name' => 'АСТ',
        ]);

        $this->assertDatabaseHas('publishers', [
            'publisher_name' => 'АСТ',
        ]);
    }

    public function test_publisher_has_many_books()
    {
        $publisher = Publisher::factory()->create();
        $books = Book::factory()->count(3)->create([
            'publisher_id' => $publisher->publisher_id,
        ]);

        $this->assertCount(3, $publisher->books);
        $this->assertInstanceOf(Book::class, $publisher->books->first());
    }

    public function test_publisher_books_relationship_uses_correct_foreign_key()
    {
        $publisher = Publisher::factory()->create();
        $book = Book::factory()->create([
            'publisher_id' => $publisher->publisher_id,
        ]);

        $this->assertTrue($publisher->books->contains($book));
        $this->assertEquals($publisher->publisher_id, $book->publisher_id);
    }

    public function test_publisher_can_have_no_books()
    {
        $publisher = Publisher::factory()->create();

        $this->assertCount(0, $publisher->books);
    }

    public function test_publisher_books_relationship_has_correct_keys()
    {
        $publisher = Publisher::factory()->create();
        
        // Проверяем, что отношение использует правильные ключи
        $relation = $publisher->books();
        
        $this->assertEquals('publisher_id', $relation->getForeignKeyName());
        $this->assertEquals('publisher_id', $relation->getLocalKeyName());
    }

    public function test_publisher_name_is_required()
    {
        $this->expectException(QueryException::class);

        Publisher::create([]);
    }

    public function test_publisher_name_can_contain_russian_letters()
    {
        $publisher = Publisher::factory()->create([
            'publisher_name' => 'Эксмо',
        ]);

        $this->assertEquals('Эксмо', $publisher->publisher_name);
    }

    public function test_publisher_name_can_contain_numbers()
    {
        $publisher = Publisher::factory()->create([
            'publisher_name' => 'O\'Reilly Media 2024',
        ]);

        $this->assertEquals('O\'Reilly Media 2024', $publisher->publisher_name);
    }

    public function test_publisher_name_can_contain_special_characters()
    {
        $publisher = Publisher::factory()->create([
            'publisher_name' => 'Penguin Random House',
        ]);

        $this->assertEquals('Penguin Random House', $publisher->publisher_name);
    }
}