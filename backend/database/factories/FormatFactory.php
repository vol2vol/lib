<?php

namespace Database\Factories;

use App\Models\Format;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormatFactory extends Factory
{
    protected $model = Format::class;

    public function definition(): array
    {
        $formats = [
            'pdf', 'djvu', 'fb2', 'txt',
            'docx', 'doc', 'rtf', 'mp3'
        ];

        return [
            'format_name' => $this->faker->unique()->randomElement($formats),
        ];
    }

}
