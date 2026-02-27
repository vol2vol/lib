<?php

namespace Database\Factories;

use App\Models\Format;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormatFactory extends Factory
{
    protected $model = Format::class;

    public function definition(): array
    {
        return [
            'format_name' => $this->faker->randomElement([
                'Электронная книга',
                'Аудиокнига',
                'Бумажная книга',
                'PDF',
                'EPUB',
                'MOBI',
                'FB2',
            ]),
        ];
    }
}