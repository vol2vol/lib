<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model
{
    use HasFactory; // для генерации данных

    protected $primaryKey = 'book_id';
    public $timestamps = true;

    protected $fillable = [
        'book_title',
        'description',
        'publisher_year',
        'publisher_id',
        'format_id',
        'file_path',
        'file_size_bytes'
    ];

    protected $casts = [
        'published_year' => 'integer',
        'file_size_bytes' => 'integer',
    ];

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'book_genres', 'book_id', 'genre_id');
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class, 'publisher_id', 'publisher_id');
    }

    public function format()
    {
        return $this->belongsTo(Format::class, 'format_id', 'format_id');
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'book_authors', 'book_id', 'author_id');
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorite_books', 'book_id', 'user_id');
    }

    public function isFavoritedByUser($userId)
    {
        return $this->favoritedBy()->where('users.user_id', $userId)->exists();
    }

    public function getAuthorsFullNameAttribute()
    {
        return $this->authors->map(function($author) {
            return trim("{$author->last_name} {$author->first_name} {$author->middle_name}");
        })->implode(', ');
    }
}
