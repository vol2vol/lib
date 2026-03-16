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

    public function files()
    {
        return $this->hasMany(BookFile::class, 'format_id', 'format_id');
    }
}
