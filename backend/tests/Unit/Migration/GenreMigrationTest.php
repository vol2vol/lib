<?php

namespace Tests\Unit\Migration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class GenreMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_genres_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('genres'));
        $this->assertTrue(Schema::hasColumn('genres', 'genre_id'));
        $this->assertTrue(Schema::hasColumn('genres', 'genre_name'));
        $this->assertTrue(Schema::hasColumn('genres', 'created_at'));
        $this->assertTrue(Schema::hasColumn('genres', 'updated_at'));
    }

    public function test_genres_table_has_primary_key()
    {
        $columns = Schema::getColumns('genres');
        $primaryKeyColumn = collect($columns)->firstWhere('name', 'genre_id');

        $this->assertTrue($primaryKeyColumn['auto_increment']);
    }

    public function test_genre_id_cannot_be_null()
    {
        $columns = Schema::getColumns('genres');
        $genreNameColumn = collect($columns)->firstWhere('name', 'genre_id');

        $this->assertFalse($genreNameColumn['nullable']);
    }

    public function test_genre_name_cannot_be_null()
    {
        $columns = Schema::getColumns('genres');
        $genreNameColumn = collect($columns)->firstWhere('name', 'genre_name');

        $this->assertFalse($genreNameColumn['nullable']);
    }
}