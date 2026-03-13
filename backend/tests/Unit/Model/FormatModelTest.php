<?php

namespace Tests\Unit\Model;

use App\Models\Format;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormatModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_format_has_correct_fillable_fields()
    {
        $format = Format::factory()->make();

        $this->assertEquals(['format_name'], $format->getFillable());
    }

    public function test_format_has_custom_primary_key()
    {
        $format = Format::factory()->create();

        $this->assertEquals('format_id', $format->getKeyName());
        $this->assertTrue($format->exists);
    }

    public function test_format_has_timestamps()
    {
        $format = Format::factory()->create();

        $this->assertNotNull($format->created_at);
        $this->assertNotNull($format->updated_at);
    }

    public function test_format_can_be_created_with_valid_data()
    {
        $format = Format::factory()->create([
            'format_name' => 'EPUB',
        ]);

        $this->assertDatabaseHas('formats', [
            'format_name' => 'EPUB',
        ]);
    }

    // public function test_format_has_many_books()
    // {
    //     $format = Format::factory()->create();
    //     $books = Book::factory()->count(3)->create([
    //         'format_id' => $format->format_id,
    //     ]);

    //     $this->assertCount(3, $format->books);
    //     $this->assertInstanceOf(Book::class, $format->books->first());
    // }

    // public function test_format_books_relationship_uses_correct_foreign_key()
    // {
    //     $format = Format::factory()->create();
    //     $book = Book::factory()->create([
    //         'format_id' => $format->format_id,
    //     ]);

    //     $this->assertTrue($format->books->contains($book));
    //     $this->assertEquals($format->format_id, $book->format_id);
    // }

    // public function test_format_can_have_no_books()
    // {
    //     $format = Format::factory()->create();

    //     $this->assertCount(0, $format->books);
    // }

    public function test_format_books_relationship_has_correct_keys()
    {
        $format = Format::factory()->create();
        
        // Проверяем, что отношение использует правильные ключи
        $relation = $format->books();
        
        $this->assertEquals('format_id', $relation->getForeignKeyName());
        $this->assertEquals('format_id', $relation->getLocalKeyName());
    }

    public function test_format_name_is_required()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Format::create([]);
    }

    // public function test_format_name_must_be_unique()
    // {
    //     Format::factory()->create(['format_name' => 'EPUB']);

    //     $this->expectException(\Illuminate\Database\QueryException::class);
    //     Format::factory()->create(['format_name' => 'EPUB']);
    // }

    public function test_format_name_can_contain_russian_letters()
    {
        $format = Format::factory()->create([
            'format_name' => 'Электронная книга',
        ]);

        $this->assertEquals('Электронная книга', $format->format_name);
    }

    public function test_format_name_can_contain_file_extensions()
    {
        $format = Format::factory()->create([
            'format_name' => 'PDF',
        ]);

        $this->assertEquals('PDF', $format->format_name);
    }

    public function test_format_name_can_contain_multiple_words()
    {
        $format = Format::factory()->create([
            'format_name' => 'Бумажная книга',
        ]);

        $this->assertEquals('Бумажная книга', $format->format_name);
    }

    public function test_format_name_can_contain_hyphens()
    {
        $format = Format::factory()->create([
            'format_name' => 'Аудио-книга',
        ]);

        $this->assertEquals('Аудио-книга', $format->format_name);
    }
}