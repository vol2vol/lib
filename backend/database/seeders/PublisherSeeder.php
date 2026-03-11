<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Publisher;

class PublisherSeeder extends Seeder
{
    public function run(): void
    {
        $knownPublishers = ['Эксмо', 'АСТ', 'МИФ'];
        foreach ($knownPublishers as $name) {
            Publisher::firstOrCreate(['publisher_name' => $name]);
        }

        Publisher::factory()->count(27)->create();
    }
}
