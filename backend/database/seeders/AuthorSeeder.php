<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Author;

class AuthorSeeder extends Seeder
{
    public function run(): void
    {
        $authors = [
            ['last_name' => 'Дюпро', 'first_name'=>'Джин'],
            ['last_name' => 'Володихин', 'first_name'=>'Дмитрий', 'middle_name'=>'Михайлович'],
            ['last_name'=>'Маркеев', 'first_name'=>'Олег', 'middle_name'=>'Георгиевич'],
            ['last_name'=>'Парнов', 'first_name'=>'Еремей', 'middle_name'=>'Иудович'],
            ['last_name'=>'Емельянов', 'first_name'=>'В.', 'middle_name'=>'М.'],
            ['last_name'=>'Рябченко', 'first_name'=>'Виктор'],
            ['last_name'=>'Рэнди Дэвис', 'first_name'=>'Стефан'],
            ['last_name'=>'Эттенборо', 'first_name'=>'Дэвид'],
            ['last_name'=>'Перельман', 'first_name'=>'Яков', 'middle_name'=>'Исидорович'],
            ['last_name'=>'Радзиевская', 'first_name'=>'Софья', 'middle_name'=>'Борисовна'],
            ['last_name'=>'Ренсом', 'first_name'=>'Риггз'],
            ['last_name'=>'Браславский', 'first_name'=>'Дмитрий', 'middle_name'=>'Юрьевич'],
            ['last_name'=>'Маркс', 'first_name'=>'Карл'],
            ['last_name'=>'Энгельс', 'first_name'=>'Фридрих'],
            ['last_name'=>'Булгаков', 'first_name'=>'Михаил', 'middle_name'=>'Афанасьевич'],
            ['last_name'=>'Пушкин', 'first_name'=>'Александр', 'middle_name'=>'Сергеевич'],
            ['last_name'=>'Фидлер', 'first_name'=>'Аркадий'],
            ['last_name'=>'Кофлер', 'first_name'=>'Михаэль'],
            ['last_name'=>'Миронова', 'first_name'=>'Татьяна', 'middle_name'=>'Леонидовна'],
            ['last_name'=>'Юстейн', 'first_name'=>'Гордер'],
            ['last_name'=>'Ламур', 'first_name'=>'Луис'],
            ['last_name'=>'Велитов', 'first_name'=>'Алим'],
            ['last_name'=>'Уштей', 'first_name'=>'Анна'],
            ['last_name'=>'Хокинг', 'first_name'=>'Стивен'],
            ['last_name'=>'Хабберт', 'first_name'=>'Марион Кинг'],
            ['last_name'=>'Ковпак', 'first_name'=>'Сидор', 'middle_name'=>'Артемьевич'],
            ['last_name'=>'Дигонский', 'first_name'=>'Сергей', 'middle_name'=>'Викторович'],
            ['last_name'=>'Тен', 'first_name'=>'Вячеслав', 'middle_name'=>'Владимирович'],
            ['last_name'=>'Аксенов', 'first_name'=>'Геннадий', 'middle_name'=>'Петрович'],
            ['last_name'=>'Гомбрих', 'first_name'=>'Эрнст'],
            ['last_name'=>'Вишняцкий', 'first_name'=>'Леонид', 'middle_name'=>'Борисович'],
            ['last_name'=>'Даррелл', 'first_name'=>'Джеральд'],
        ];

        foreach ($authors as $author) {
            Author::firstOrCreate($author);
        }
    }
}
