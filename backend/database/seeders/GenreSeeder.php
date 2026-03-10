<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Genre;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            'Роман', 'Фантастика', 'Детектив', 'Научная фантастика',
            'Фэнтези', 'Триллер', 'Приключения', 'Исторический роман',
            'Поэзия', 'Драма', 'Биография', 'Мемуары',
            'Классика', 'Современная проза', 'Ужасы', 'Мистика',
            'Психология', 'Философия', 'Религия', 'Искусство',
            'Публицистика', 'Наука', 'Техника', 'Спорт',
            'Кулинария', 'Путешествия', 'Юмор', 'Сказки',
            'Молодежная проза', 'Любовный роман'
        ];

        foreach ($genres as $genre) {
            Genre::firstOrCreate(['genre_name' => $genre]);
        }
    }
}
