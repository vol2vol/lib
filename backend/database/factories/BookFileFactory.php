<?php

namespace Database\Factories;

use App\Models\BookFile;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFileFactory extends Factory
{
    protected $model = BookFile::class;

    public function definition(): array
    {
        // Простые расширения
        $extensions = ['pdf', 'txt', 'fb2'];
        $ext = $this->faker->randomElement($extensions);

        return [
            'book_id' => Book::factory(),
            'format_id' => rand(1, 3),
            'file_path' => 'books/' . $this->faker->uuid() . '.' . $ext,
            'file_size_bytes' => $this->faker->numberBetween(100000, 50000000),
        ];
    }

    /**
     * Прикрепить файл к конкретной книге
     */
    public function forBook(Book $book): static
    {
        return $this->state(fn (array $attrs) => [
            'book_id' => $book->book_id,
        ]);
    }

    /**
     * Создать PDF файл
     */
    public function pdf(): static
    {
        return $this->state(fn (array $attrs) => [
            'format_id' => 1, // PDF
            'file_path' => 'books/' . $this->faker->uuid() . '.pdf',
        ]);
    }

    /**
     * Создать TXT файл
     */
    public function txt(): static
    {
        return $this->state(fn (array $attrs) => [
            'format_id' => 2, // TXT
            'file_path' => 'books/' . $this->faker->uuid() . '.txt',
        ]);
    }

    /**
     * Создать FB2 файл
     */
    public function fb2(): static
    {
        return $this->state(fn (array $attrs) => [
            'format_id' => 3, // FB2
            'file_path' => 'books/' . $this->faker->uuid() . '.fb2',
        ]);
    }

    /**
     * Большой файл (>10MB)
     */
    public function largeFile(): static
    {
        return $this->state(fn (array $attrs) => [
            'file_size_bytes' => $this->faker->numberBetween(10, 50) * 1024 * 1024,
        ]);
    }

    /**
     * Маленький файл (<1MB)
     */
    public function smallFile(): static
    {
        return $this->state(fn (array $attrs) => [
            'file_size_bytes' => $this->faker->numberBetween(100, 900) * 1024,
        ]);
    }

    /**
     * Указать конкретный размер
     */
    public function withSize(int $bytes): static
    {
        return $this->state(fn (array $attrs) => [
            'file_size_bytes' => $bytes,
        ]);
    }
}
