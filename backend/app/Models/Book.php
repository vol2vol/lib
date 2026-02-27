<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $primaryKey = 'book_id';
    public $timestamps = true;

    protected $fillable = [
        'book_title',
        'description',
        'publisher_id',
        'format_id',
        'file_path',
        'file_size_bytes'
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
}
