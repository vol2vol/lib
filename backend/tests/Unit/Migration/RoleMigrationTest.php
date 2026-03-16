<?php

namespace Tests\Unit\Migration;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RoleMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_roles_table_has_expected_columns()
    {
        $this->assertTrue(Schema::hasTable('roles'));
        $this->assertTrue(Schema::hasColumn('roles', 'role_id'));
        $this->assertTrue(Schema::hasColumn('roles', 'role_name'));
        $this->assertTrue(Schema::hasColumn('roles', 'created_at'));
        $this->assertTrue(Schema::hasColumn('roles', 'updated_at'));
    }

    public function test_roles_table_has_primary_key()
    {
        $columns = Schema::getColumns('roles');
        $primaryKeyColumn = collect($columns)->firstWhere('name', 'role_id');

        $this->assertTrue($primaryKeyColumn['auto_increment']);
    }

    public function test_role_id_cannot_be_null()
    {
        $columns = Schema::getColumns('roles');
        $publisherNameColumn = collect($columns)->firstWhere('name', 'role_id');

        $this->assertFalse($publisherNameColumn['nullable']);
    }

    public function test_role_name_cannot_be_null()
    {
        $columns = Schema::getColumns('roles');
        $publisherNameColumn = collect($columns)->firstWhere('name', 'role_name');

        $this->assertFalse($publisherNameColumn['nullable']);
    }
}