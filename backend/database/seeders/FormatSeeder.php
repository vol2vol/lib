<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Format;

class FormatSeeder extends Seeder
{
    public function run(): void
    {
        Format::truncate();

        Format::create([
            'format_id' => 1,
            'format_name' => 'PDF'
        ]);

        Format::create([
            'format_id' => 2,
            'format_name' => 'TXT'
        ]);

        Format::create([
            'format_id' => 3,
            'format_name' => 'FB2'
        ]);
    }
}
