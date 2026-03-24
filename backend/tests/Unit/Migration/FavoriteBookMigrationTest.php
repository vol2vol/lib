<?php

namespace Tests\Unit\Migration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FavoriteBookMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_favorite_books_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('favorite_books'));
        $this->assertTrue(Schema::hasColumn('favorite_books', 'user_id'));
        $this->assertTrue(Schema::hasColumn('favorite_books', 'book_id'));
        $this->assertTrue(Schema::hasColumn('favorite_books', 'created_at'));
        $this->assertTrue(Schema::hasColumn('favorite_books', 'updated_at'));
    }

    public function test_book_genres_table_has_primary_key()
    {
        $columns = Schema::getColumns('favorite_books');
        $primaryKeyColumn1 = collect($columns)->firstWhere('name', 'user_id');
        $primaryKeyColumn2 = collect($columns)->firstWhere('name', 'book_id');

        $this->assertFalse($primaryKeyColumn1['nullable']);
        $this->assertFalse($primaryKeyColumn2['nullable']);
    }
}