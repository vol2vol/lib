<?php

namespace Tests\Unit\Model;

use App\Models\BookFile;
use App\Models\Book;
use App\Models\Format;
use App\Models\Publisher;
use Database\Seeders\FormatSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Database\QueryException;

class BookFileModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(FormatSeeder::class);
    }

    public function test_book_file_has_correct_fillable_fields()
    {
        $bookFile = BookFile::factory()->make();

        $this->assertEquals([
            'book_id',
            'format_id',
            'file_path',
            'file_size_bytes',
        ], $bookFile->getFillable());
    }

    public function test_book_file_has_custom_primary_key()
    {
        $bookFile = BookFile::factory()->create();

        $this->assertEquals('file_id', $bookFile->getKeyName());
        $this->assertTrue($bookFile->exists);
    }

    public function test_book_file_has_timestamps()
    {
        $bookFile = BookFile::factory()->create();

        $this->assertNotNull($bookFile->created_at);
        $this->assertNotNull($bookFile->updated_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $bookFile->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $bookFile->updated_at);
    }

    public function test_book_file_can_be_created_with_valid_data()
    {
        $book = Book::factory()->create();
        $format = Format::factory()->create(['format_name' => 'DOC']);

        $bookFile = BookFile::factory()->create([
            'book_id' => $book->book_id,
            'format_id' => $format->format_id,
            'file_path' => 'books/test_book.doc',
            'file_size_bytes' => 2048576,
        ]);

        $this->assertDatabaseHas('book_files', [
            'file_path' => 'books/test_book.doc',
            'file_size_bytes' => 2048576,
        ]);
    }

    public function test_file_size_bytes_is_cast_to_integer()
    {
        $bookFile = BookFile::factory()->create([
            'file_size_bytes' => '1048576',
        ]);

        $this->assertIsInt($bookFile->file_size_bytes);
        $this->assertEquals(1048576, $bookFile->file_size_bytes);
    }

    public function test_book_id_is_required()
    {
        $this->expectException(QueryException::class);

        BookFile::create([
            'format_id' => 1,
            'file_path' => 'books/test.pdf',
            'file_size_bytes' => 1024,
        ]);
    }

    public function test_format_id_is_required()
    {
        $this->expectException(QueryException::class);

        BookFile::create([
            'book_id' => 1,
            'file_path' => 'books/test.pdf',
            'file_size_bytes' => 1024,
        ]);
    }

    public function test_file_path_is_required()
    {
        $this->expectException(QueryException::class);

        BookFile::create([
            'book_id' => 1,
            'format_id' => 1,
            'file_size_bytes' => 1024,
        ]);
    }

    public function test_file_size_bytes_is_required()
    {
        $this->expectException(QueryException::class);

        BookFile::create([
            'book_id' => 1,
            'format_id' => 1,
            'file_path' => 'books/test.pdf',
        ]);
    }

    public function test_book_file_belongs_to_book()
    {
        $book = Book::factory()->create(['book_title' => 'Мастер и Маргарита']);
        $format = Format::factory()->create(['format_name' => 'EPUB']);
        $bookFile = BookFile::factory()->create([
            'book_id' => $book->book_id,
            'format_id' => $format->format_id,
        ]);

        $this->assertInstanceOf(Book::class, $bookFile->book);
        $this->assertEquals($book->book_id, $bookFile->book->book_id);
        $this->assertEquals('Мастер и Маргарита', $bookFile->book->book_title);
    }

    public function test_book_file_belongs_to_format()
    {
        $book = Book::factory()->create();
        $format = Format::factory()->create(['format_name' => 'RTF']);
        $bookFile = BookFile::factory()->create([
            'book_id' => $book->book_id,
            'format_id' => $format->format_id,
        ]);

        $this->assertInstanceOf(Format::class, $bookFile->format);
        $this->assertEquals($format->format_id, $bookFile->format->format_id);
        $this->assertEquals('RTF', $bookFile->format->format_name);
    }

    public function test_get_file_size_in_mb_attribute()
    {
        $bookFile = BookFile::factory()->create([
            'file_size_bytes' => 2097152,
        ]);

        $this->assertEquals(2.0, $bookFile->file_size_in_mb);
    }

    public function test_get_file_size_in_mb_attribute_with_decimal()
    {
        $bookFile = BookFile::factory()->create([
            'file_size_bytes' => 1572864,
        ]);

        $this->assertEquals(1.5, $bookFile->file_size_in_mb);
    }

    public function test_get_file_size_in_mb_attribute_rounds_to_two_decimals()
    {
        $bookFile = BookFile::factory()->create([
            'file_size_bytes' => 1234567,
        ]);

        $this->assertEquals(1.18, $bookFile->file_size_in_mb);
    }

    public function test_get_file_extension_attribute_with_pdf()
    {
        $bookFile = BookFile::factory()->create([
            'file_path' => 'storage/books/document.pdf',
        ]);

        $this->assertEquals('pdf', $bookFile->file_extension);
    }

    public function test_get_file_extension_attribute_with_epub()
    {
        $bookFile = BookFile::factory()->create([
            'file_path' => 'books/book.epub',
        ]);

        $this->assertEquals('epub', $bookFile->file_extension);
    }

    public function test_get_file_extension_attribute_with_fb2()
    {
        $bookFile = BookFile::factory()->create([
            'file_path' => 'books/test_file.fb2',
        ]);

        $this->assertEquals('fb2', $bookFile->file_extension);
    }

    public function test_get_file_extension_attribute_without_extension()
    {
        $bookFile = BookFile::factory()->create([
            'file_path' => 'books/no_extension',
        ]);

        $this->assertEquals('', $bookFile->file_extension);
    }

    public function test_get_file_name_attribute()
    {
        $bookFile = BookFile::factory()->create([
            'file_path' => 'books/war_and_peace.pdf',
        ]);

        $this->assertEquals('war_and_peace.pdf', $bookFile->file_name);
    }

    public function test_get_file_name_attribute_with_cyrillic_path()
    {
        $bookFile = BookFile::factory()->create([
            'file_path' => 'books/война_и_мир.epub',
        ]);

        $this->assertEquals('война_и_мир.epub', $bookFile->file_name);
    }

    public function test_get_file_name_attribute_with_special_characters()
    {
        $bookFile = BookFile::factory()->create([
            'file_path' => 'books/book (2024 edition).fb2',
        ]);

        $this->assertEquals('book (2024 edition).fb2', $bookFile->file_name);
    }

    public function test_book_file_relationships_use_correct_foreign_keys()
    {
        $book = Book::factory()->create();
        $format = Format::factory()->create();
        $bookFile = BookFile::factory()->make();

        $bookFile->book()->associate($book);
        $bookFile->format()->associate($format);
        $bookFile->save();

        $this->assertEquals($book->book_id, $bookFile->book_id);
        $this->assertEquals($format->format_id, $bookFile->format_id);
        $this->assertDatabaseHas('book_files', [
            'file_id' => $bookFile->file_id,
            'book_id' => $book->book_id,
            'format_id' => $format->format_id,
        ]);
    }

    public function test_book_file_can_have_zero_file_size()
    {
        $bookFile = BookFile::factory()->create([
            'file_size_bytes' => 0,
        ]);

        $this->assertEquals(0, $bookFile->file_size_bytes);
        $this->assertEquals(0.0, $bookFile->file_size_in_mb);
    }

    public function test_book_file_can_have_large_file_size()
    {
        $largeSize = 1073741824;
        $bookFile = BookFile::factory()->create([
            'file_size_bytes' => $largeSize,
        ]);

        $this->assertEquals($largeSize, $bookFile->file_size_bytes);
        $this->assertEquals(1024.0, $bookFile->file_size_in_mb);
    }

    public function test_file_path_can_contain_nested_directories()
    {
        $deepPath = 'books/2024/russian/classics/tolstoy/anna_karenina.fb2';
        $bookFile = BookFile::factory()->create([
            'file_path' => $deepPath,
        ]);

        $this->assertEquals($deepPath, $bookFile->file_path);
        $this->assertEquals('anna_karenina.fb2', $bookFile->file_name);
        $this->assertEquals('fb2', $bookFile->file_extension);
    }

    public function test_file_path_can_contain_spaces()
    {
        $bookFile = BookFile::factory()->create([
            'file_path' => 'books/book title.pdf',
        ]);

        $this->assertEquals('book title.pdf', $bookFile->file_name);
        $this->assertEquals('pdf', $bookFile->file_extension);
    }

    public function test_timestamps_are_updated_on_save()
    {
        $bookFile = BookFile::factory()->create();
        $originalUpdatedAt = $bookFile->updated_at;

        sleep(1);

        $bookFile->file_size_bytes = 2048;
        $bookFile->save();
        $bookFile->refresh();

        $this->assertNotEquals($originalUpdatedAt->timestamp, $bookFile->updated_at->timestamp);
        $this->assertGreaterThan($originalUpdatedAt->timestamp, $bookFile->updated_at->timestamp);
    }
}