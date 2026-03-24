<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Author;
use App\Models\Genre;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        return [
            'book_title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(3),
            'published_year' => $this->faker->numberBetween(1800, date('Y')),
            'cover_path' => 'covers/' . $this->faker->uuid() . '.jpg',
            'publisher_id' => Publisher::factory(),
        ];
    }

    public function withGenres(int $count = 2): static
    {
        return $this->afterCreating(function (Book $book) use ($count) {
            $book->genres()->attach(
                Genre::factory()->count($count)->create()
            );
        });
    }

    public function withAuthors(int $count = 1): static
    {
        return $this->afterCreating(function (Book $book) use ($count) {
            $book->authors()->attach(
                Author::factory()->count($count)->create()
            );
        });
    }

    public function withGenre(Genre $genre): static
    {
        return $this->afterCreating(function (Book $book) use ($genre) {
            $book->genres()->attach($genre);
        });
    }

    public function withAuthor($author): static
    {
        return $this->afterCreating(function (Book $book) use ($author) {
            $book->authors()->attach($author);
        });
    }
}
