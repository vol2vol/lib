<?php

namespace App\Models;

use App\Models\BookFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory; // для генерации данных

    protected $primaryKey = 'book_id';
    public $timestamps = true;

    protected $fillable = [
        'book_title',
        'description',
        'published_year',
        'cover_path',
        'publisher_id',
    ];

    protected $casts = [
        'published_year' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'book_genres', 'book_id', 'genre_id');
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class, 'publisher_id', 'publisher_id');
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'book_authors', 'book_id', 'author_id');
    }

    public function files()
    {
        return $this->hasMany(BookFile::class, 'book_id', 'book_id');
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
