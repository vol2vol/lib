<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $primaryKey = 'genre_id';
    public $timestamps = true;
    protected $fillable = ['genre_name'];

    public function books()
    {
        return $this->hasMany(Book::class, 'genre_id', 'genre_id');
    }
}
