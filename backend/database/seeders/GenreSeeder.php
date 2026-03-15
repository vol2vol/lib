<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Genre;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            ['genre_name' => 'Антиутопия'],
            ['genre_name' => 'Биографии и мемуары'],
            ['genre_name' => 'Научная фантастика'],
            ['genre_name' => 'Боевики'],
            ['genre_name' => 'Криминальные детективы'],
            ['genre_name' => 'Исторические детективы'],
            ['genre_name' => 'Автомобили и ПДД'],
            ['genre_name' => 'Базы данных'],
            ['genre_name' => 'Детская образовательная литература'],
            ['genre_name' => 'Программирование'],
            ['genre_name' => 'Природа и животные'],
            ['genre_name' => 'Биология'],
            ['genre_name' => 'Физика'],
            ['genre_name' => 'Военная проза'],
            ['genre_name' => 'Исторические приключения'],
            ['genre_name' => 'Мистика'],
            ['genre_name' => 'Городское фэнтези'],
            ['genre_name' => 'Фэнтези'],
            ['genre_name' => 'Книга-игра'],
            ['genre_name' => 'Философия'],
            ['genre_name' => 'Политика'],
            ['genre_name' => 'История'],
            ['genre_name' => 'Государство и право'],
            ['genre_name' => 'Классическая проза'],
            ['genre_name' => 'Роман'],
            ['genre_name' => 'Сказки'],
            ['genre_name' => 'Детские стихи'],
            ['genre_name' => 'Приключения про индейцев'],
            ['genre_name' => 'Путешествия и география'],
            ['genre_name' => 'Математика'],
            ['genre_name' => 'Справочники'],
            ['genre_name' => 'Руководства'],
            ['genre_name' => 'Учебники'],
            ['genre_name' => 'Публицистика'],
            ['genre_name' => 'Современная проза'],
            ['genre_name' => 'Вестерны'],
            ['genre_name' => 'Кулинария'],
            ['genre_name' => 'Научпоп'],
            ['genre_name' => 'Астрономия и Космос'],
            ['genre_name' => 'Геология и география'],
            ['genre_name' => 'Технические науки'],
            ['genre_name' => 'Военная документалистика'],
            ['genre_name' => 'Военная история'],
            ['genre_name' => 'Физическая химия'],
            ['genre_name' => 'Экология'],
            ['genre_name' => 'Искусство и Дизайн'],
            ['genre_name' => 'Обществознание'],
            ['genre_name' => 'Зоология'],
        ];

        foreach ($genres as $genre) {
            Genre::firstOrCreate($genre);
        }
    }
}
