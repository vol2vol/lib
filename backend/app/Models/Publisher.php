<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Publisher extends Model
{
    use HasFactory; // для генерации данных
    
    protected $primaryKey = 'publisher_id';
    public $timestamps = true;
    protected $fillable = ['publisher_name'];

    public function books()
    {
        return $this->hasMany(Book::class, 'publisher_id', 'pushlisher_id');
    }
}
