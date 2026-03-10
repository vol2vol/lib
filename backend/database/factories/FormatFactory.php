<?php

namespace Database\Factories;

use App\Models\Format;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormatFactory extends Factory
{
    protected $model = Format::class;

    public function definition(): array
    {
        // Возвращаем существующий формат
        return [
            'format_name' => 'PDF'
        ];
    }

    // Переопределяем метод создания - НЕ создаем новые форматы
    public function create($attributes = [], ?\Illuminate\Database\Eloquent\Model $parent = null)
    {
        // Просто возвращаем существующий формат с ID=1
        return Format::find(1) ?? parent::create($attributes, $parent);
    }
}
