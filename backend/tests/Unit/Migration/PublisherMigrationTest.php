<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PublisherMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_publishers_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('publishers'));
        $this->assertTrue(Schema::hasColumn('publishers', 'publisher_id'));
        $this->assertTrue(Schema::hasColumn('publishers', 'publisher_name'));
        $this->assertTrue(Schema::hasColumn('publishers', 'created_at'));
        $this->assertTrue(Schema::hasColumn('publishers', 'updated_at'));
    }

    public function test_publishers_table_has_primary_key()
    {
        $columns = Schema::getColumns('publishers');
        $primaryKeyColumn = collect($columns)->firstWhere('name', 'publisher_id');

        $this->assertTrue($primaryKeyColumn['auto_increment']);
    }

    public function test_publisher_name_cannot_be_null()
    {
        $columns = Schema::getColumns('publishers');
        $publisherNameColumn = collect($columns)->firstWhere('name', 'publisher_name');

        $this->assertFalse($publisherNameColumn['nullable']);
    }
}