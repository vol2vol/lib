<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuthorFactory extends Factory
{
    protected $model = Author::class;

    /**
     * Распространённые русские отчества
     */
    protected array $russianMiddleNames = [
        'Александрович', 'Александровна',
        'Дмитриевич', 'Дмитриевна',
        'Сергеевич', 'Сергеевна',
        'Иванович', 'Ивановна',
        'Николаевич', 'Николаевна',
        'Владимирович', 'Владимировна',
        'Павлович', 'Павловна',
        'Михайлович', 'Михайловна',
        'Андреевич', 'Андреевна',
        'Юрьевич', 'Юрьевна',
        'Викторович', 'Викторовна',
        'Петрович', 'Петровна',
        'Борисович', 'Борисовна',
        'Олегович', 'Олеговна',
        'Валерьевич', 'Валерьевна',
    ];

    public function definition(): array
    {
        return [
            'last_name' => $this->faker->lastName(),
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->optional(0.8)->randomElement($this->russianMiddleNames),
        ];
    }

    /**
     * Указать полное имя автора
     */
    public function withFullName(string $first, string $middle, string $last): static
    {
        return $this->state([
            'first_name' => $first,
            'middle_name' => $middle,
            'last_name' => $last,
        ]);
    }

    /**
     * Указать автора без отчества
     */
    public function withoutMiddleName(): static
    {
        return $this->state([
            'middle_name' => null,
        ]);
    }

    /**
     * Указать автора с конкретным отчеством
     */
    public function withMiddleName(string $middleName): static
    {
        return $this->state([
            'middle_name' => $middleName,
        ]);
    }
}