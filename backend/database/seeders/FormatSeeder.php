<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Format;

class FormatSeeder extends Seeder
{
    public function run(): void
    {
        $formats = [
            ['format_name' => 'PDF'],
            ['format_name' => 'TXT'],
            ['format_name' => 'FB2']
        ];

        foreach ($formats as $format) {
            Format::firstOrCreate($format);
        }
    }
}
