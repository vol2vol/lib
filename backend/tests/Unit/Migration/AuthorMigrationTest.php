<?php

namespace Tests\Unit\Migration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AuthorMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authors_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('authors'));
        $this->assertTrue(Schema::hasColumn('authors', 'author_id'));
        $this->assertTrue(Schema::hasColumn('authors', 'last_name'));
        $this->assertTrue(Schema::hasColumn('authors', 'first_name'));
        $this->assertTrue(Schema::hasColumn('authors', 'middle_name'));
        $this->assertTrue(Schema::hasColumn('authors', 'created_at'));
        $this->assertTrue(Schema::hasColumn('authors', 'updated_at'));
    }

    public function test_authors_table_has_primary_key()
    {
        $columns = Schema::getColumns('authors');
        $primaryKeyColumn = collect($columns)->firstWhere('name', 'author_id');

        $this->assertTrue($primaryKeyColumn['auto_increment']);
    }

    public function test_author_id_cannot_be_null()
    {
        $columns = Schema::getColumns('authors');
        $genreNameColumn = collect($columns)->firstWhere('name', 'author_id');

        $this->assertFalse($genreNameColumn['nullable']);
    }

    public function test_last_name_cannot_be_null()
    {
        $columns = Schema::getColumns('authors');
        $genreNameColumn = collect($columns)->firstWhere('name', 'last_name');

        $this->assertFalse($genreNameColumn['nullable']);
    }

    public function test_first_name_cannot_be_null()
    {
        $columns = Schema::getColumns('authors');
        $genreNameColumn = collect($columns)->firstWhere('name', 'first_name');

        $this->assertFalse($genreNameColumn['nullable']);
    }
}