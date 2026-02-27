<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\Author;
use App\Models\Genre;
use App\Models\Publisher;
use App\Models\Format;
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
            'publisher_id' => Publisher::factory(),
            'format_id' => Format::factory(),
            'file_path' => 'books/' . $this->faker->uuid() . '.pdf',
            'file_size_bytes' => $this->faker->numberBetween(100000, 10000000), // 100KB - 10MB
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

    public function largeFile(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'file_size_bytes' => 50 * 1024 * 1024, // 50MB
            ];
        });
    }

    public function smallFile(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'file_size_bytes' => 100 * 1024, // 100KB
            ];
        });
    }
}