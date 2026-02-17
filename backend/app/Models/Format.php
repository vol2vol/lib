<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Format extends Model
{
    protected $primaryKey = 'format_id';
    public $timestamps = true;
    protected $fillable = ['format_name'];

    public function books()
    {
        return $this->hasMany(Book::class, 'genre_id', 'genre_id');
    }
}
