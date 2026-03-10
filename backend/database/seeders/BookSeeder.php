<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Author;
use App\Models\Genre;
use App\Models\Publisher;
use App\Models\BookFile;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $authors = Author::all();
        $genres = Genre::all();
        $publishers = Publisher::all();

        if ($authors->count() < 30 || $genres->count() < 20 || $publishers->count() < 30) {
            $this->command->error('Сначала запустите AuthorSeeder, GenreSeeder и PublisherSeeder!');
            return;
        }

        Book::factory()
            ->count(30)
            ->make()
            ->each(function ($book) use ($authors, $genres, $publishers) {
                $book->publisher_id = $publishers->random()->publisher_id;
                $book->save();

                $book->authors()->attach(
                    $authors->random(rand(1, 3))->pluck('author_id')
                );

                $book->genres()->attach(
                    $genres->random(rand(1, 2))->pluck('genre_id')
                );

                $formats = [1, 2, 3];
                $numFiles = rand(1, 3);
                $selectedFormats = array_rand(array_flip($formats), $numFiles);

                if (!is_array($selectedFormats)) {
                    $selectedFormats = [$selectedFormats];
                }

                foreach ($selectedFormats as $formatId) {
                    $ext = match($formatId) {
                        1 => 'pdf',
                        2 => 'txt',
                        3 => 'fb2',
                        default => 'pdf'
                    };

                    BookFile::factory()->create([
                        'book_id' => $book->book_id,
                        'format_id' => $formatId,
                        'file_path' => 'books/' . fake()->uuid() . '.' . $ext,
                    ]);
                }
            });
    }
}
