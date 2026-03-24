<?php

namespace Database\Factories;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Factory;

class GenreFactory extends Factory
{
    protected $model = Genre::class;

    public function definition(): array
    {
        $genres = [
            'Роман', 'Фантастика', 'Детектив', 'Научная фантастика',
            'Фэнтези', 'Триллер', 'Приключения', 'Исторический роман',
            'Поэзия', 'Драма', 'Комедия', 'Биография', 'Мемуары',
            'Классика', 'Современная проза', 'Ужасы', 'Мистика',
            'Манга', 'Психология', 'Медицина', 'Мелодрама', 'Повесть',
            'Поэма', 'Баллада', 'Рассказ'
        ];

        return [
            'genre_name' => $this->faker->unique()->randomElement($genres),
        ];
    }
}
