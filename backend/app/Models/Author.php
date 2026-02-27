<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Author extends Model
{
    use HasFactory; // для генерации данных

    protected $primaryKey = 'author_id';
    public $timestamps = true;

    protected $fillable = [
        'last_name',
        'first_name',
        'middle_name'
    ];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_authors', 'author_id', 'book_id');
    }
}
