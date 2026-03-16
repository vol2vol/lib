<?php

namespace Tests\Unit\Migration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BookAuthorMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_authors_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('book_authors'));
        $this->assertTrue(Schema::hasColumn('book_authors', 'book_id'));
        $this->assertTrue(Schema::hasColumn('book_authors', 'author_id'));
    }

    public function test_book_authors_table_has_primary_key()
    {
        $columns = Schema::getColumns('book_authors');
        $primaryKeyColumn1 = collect($columns)->firstWhere('name', 'book_id');
        $primaryKeyColumn2 = collect($columns)->firstWhere('name', 'author_id');

        $this->assertFalse($primaryKeyColumn1['nullable']);
        $this->assertFalse($primaryKeyColumn2['nullable']);
    }
}