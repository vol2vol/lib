<?php

namespace Tests\Unit\Migration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BookGenreMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_genres_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('book_genres'));
        $this->assertTrue(Schema::hasColumn('book_genres', 'book_id'));
        $this->assertTrue(Schema::hasColumn('book_genres', 'genre_id'));
    }

    public function test_book_genres_table_has_primary_key()
    {
        $columns = Schema::getColumns('book_genres');
        $primaryKeyColumn1 = collect($columns)->firstWhere('name', 'book_id');
        $primaryKeyColumn2 = collect($columns)->firstWhere('name', 'genre_id');

        $this->assertFalse($primaryKeyColumn1['nullable']);
        $this->assertFalse($primaryKeyColumn2['nullable']);
    }
}