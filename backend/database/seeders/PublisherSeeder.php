<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class PublisherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('publishers')->insert([
            ['publisher_id' => 1, 'publisher_name' => 'Издательство1'],
            ['publisher_id' => 2, 'publisher_name' => 'Издательство2'],
            ['publisher_id' => 3, 'publisher_name' => 'Издательство3'],
        ]);
    }
}
