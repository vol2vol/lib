<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Format extends Model
{
    use HasFactory; // для генерации данных

    protected $primaryKey = 'format_id';
    public $timestamps = true;
    protected $fillable = ['format_name'];

    public function bookFiles()
    {
        return $this->hasMany(BookFile::class, 'format_id', 'format_id');
    }

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_files', 'format_id', 'book_id');
    }
}
