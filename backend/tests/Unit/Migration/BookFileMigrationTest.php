<?php

namespace Tests\Unit\Migration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BookFileMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_files_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('book_files'));
        $this->assertTrue(Schema::hasColumn('book_files', 'file_id'));
        $this->assertTrue(Schema::hasColumn('book_files', 'book_id'));
        $this->assertTrue(Schema::hasColumn('book_files', 'format_id'));
        $this->assertTrue(Schema::hasColumn('book_files', 'file_path'));
        $this->assertTrue(Schema::hasColumn('book_files', 'file_size_bytes'));
        $this->assertTrue(Schema::hasColumn('book_files', 'created_at'));
        $this->assertTrue(Schema::hasColumn('book_files', 'updated_at'));
    }

    public function test_book_files_table_has_primary_key()
    {
        $columns = Schema::getColumns('book_files');
        $primaryKeyColumn = collect($columns)->firstWhere('name', 'file_id');

        $this->assertTrue($primaryKeyColumn['auto_increment']);
    }

    public function test_file_id_cannot_be_null()
    {
        $columns = Schema::getColumns('book_files');
        $genreNameColumn = collect($columns)->firstWhere('name', 'file_id');

        $this->assertFalse($genreNameColumn['nullable']);
    }

    public function test_book_id_cannot_be_null()
    {
        $columns = Schema::getColumns('book_files');
        $genreNameColumn = collect($columns)->firstWhere('name', 'book_id');

        $this->assertFalse($genreNameColumn['nullable']);
    }

    public function test_format_id_cannot_be_null()
    {
        $columns = Schema::getColumns('book_files');
        $genreNameColumn = collect($columns)->firstWhere('name', 'format_id');

        $this->assertFalse($genreNameColumn['nullable']);
    }

    public function test_file_path_cannot_be_null()
    {
        $columns = Schema::getColumns('book_files');
        $genreNameColumn = collect($columns)->firstWhere('name', 'file_path');

        $this->assertFalse($genreNameColumn['nullable']);
    }
}