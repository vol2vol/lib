<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Genre extends Model
{
    use HasFactory; // для генерации данных

    protected $primaryKey = 'genre_id';
    public $timestamps = true;
    protected $fillable = ['genre_name'];

    public function books()
    {
        return $this->hasMany(Book::class, 'genre_id', 'genre_id');
    }
}
