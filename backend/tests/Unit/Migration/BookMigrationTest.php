<?php

namespace Tests\Unit\Migration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BookMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_books_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('books'));
        $this->assertTrue(Schema::hasColumn('books', 'book_id'));
        $this->assertTrue(Schema::hasColumn('books', 'book_title'));
        $this->assertTrue(Schema::hasColumn('books', 'description'));
        $this->assertTrue(Schema::hasColumn('books', 'published_year'));
        $this->assertTrue(Schema::hasColumn('books', 'publisher_id'));
        $this->assertTrue(Schema::hasColumn('books', 'cover_path'));
        $this->assertTrue(Schema::hasColumn('books', 'created_at'));
        $this->assertTrue(Schema::hasColumn('books', 'updated_at'));
    }

    public function test_books_table_has_primary_key()
    {
        $columns = Schema::getColumns('books');
        $primaryKeyColumn = collect($columns)->firstWhere('name', 'book_id');

        $this->assertTrue($primaryKeyColumn['auto_increment']);
    }

    public function test_book_id_cannot_be_null()
    {
        $columns = Schema::getColumns('books');
        $genreNameColumn = collect($columns)->firstWhere('name', 'book_id');

        $this->assertFalse($genreNameColumn['nullable']);
    }

    public function test_book_title_cannot_be_null()
    {
        $columns = Schema::getColumns('books');
        $genreNameColumn = collect($columns)->firstWhere('name', 'book_title');

        $this->assertFalse($genreNameColumn['nullable']);
    }

    public function test_description_cannot_be_null()
    {
        $columns = Schema::getColumns('books');
        $genreNameColumn = collect($columns)->firstWhere('name', 'description');

        $this->assertFalse($genreNameColumn['nullable']);
    }

    public function test_published_year_cannot_be_null()
    {
        $columns = Schema::getColumns('books');
        $genreNameColumn = collect($columns)->firstWhere('name', 'published_year');

        $this->assertFalse($genreNameColumn['nullable']);
    }

    public function test_publisher_id_cannot_be_null()
    {
        $columns = Schema::getColumns('books');
        $genreNameColumn = collect($columns)->firstWhere('name', 'publisher_id');

        $this->assertFalse($genreNameColumn['nullable']);
    }

}