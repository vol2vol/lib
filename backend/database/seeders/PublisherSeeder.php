<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Publisher;

class PublisherSeeder extends Seeder
{
    public function run(): void
    {
        $publishers = [
            ['publisher_name' => 'Махаон'],
            ['publisher_name' => 'Молодая гвардия'],
            ['publisher_name' => 'ОЛМА-ПРЕСС'],
            ['publisher_name' => 'Детская литература'],
            ['publisher_name' => 'Бук-Пресс'],
            ['publisher_name' => 'Омское книжное издательство'],
            ['publisher_name' => 'Диалектика'],
            ['publisher_name' => 'Мир'],
            ['publisher_name' => 'Наука'],
            ['publisher_name' => 'Татарское книжное издательство'],
            ['publisher_name' => 'Книжный клуб "Клуб Семейного Досуга"'],
            ['publisher_name' => 'Производственно-коммерческий центр "АТ"'],
            ['publisher_name' => 'Государственное издательство политической литературы'],
            ['publisher_name' => 'Ленинград'],
            ['publisher_name' => 'Питер'],
            ['publisher_name' => 'Алгоритм'],
            ['publisher_name' => 'Амфора'],
            ['publisher_name' => 'Центрполинраф'],
            ['publisher_name' => 'Росмэн'],
            ['publisher_name' => 'Api'],
            ['publisher_name' => 'Воениздат НКО СССР'],
            ['publisher_name' => 'Эдиториал УРСС'],
            ['publisher_name' => 'АСТ'],
            ['publisher_name' => 'Век2'],
            ['publisher_name' => 'Воздушный транспорт'],
        ];

        foreach ($publishers as $publisher) {
            Publisher::firstOrCreate($publisher);
        }
    }
}
