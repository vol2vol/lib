<?php

namespace Tests\Unit\Migration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FormatMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_formats_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('formats'));
        $this->assertTrue(Schema::hasColumn('formats', 'format_id'));
        $this->assertTrue(Schema::hasColumn('formats', 'format_name'));
        $this->assertTrue(Schema::hasColumn('formats', 'created_at'));
        $this->assertTrue(Schema::hasColumn('formats', 'updated_at'));
    }

    public function test_formats_table_has_primary_key()
    {
        $columns = Schema::getColumns('formats');
        $primaryKeyColumn = collect($columns)->firstWhere('name', 'format_id');

        $this->assertTrue($primaryKeyColumn['auto_increment']);
    }

    public function test_format_id_cannot_be_null()
    {
        $columns = Schema::getColumns('formats');
        $genreNameColumn = collect($columns)->firstWhere('name', 'format_id');

        $this->assertFalse($genreNameColumn['nullable']);
    }

    public function test_format_name_cannot_be_null()
    {
        $columns = Schema::getColumns('formats');
        $genreNameColumn = collect($columns)->firstWhere('name', 'format_name');

        $this->assertFalse($genreNameColumn['nullable']);
    }
}