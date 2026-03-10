<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookFile extends Model
{
    use HasFactory;

    protected $primaryKey = 'file_id';
    public $timestamps = true;

    protected $fillable = [
        'book_id',
        'format_id',
        'file_path',
        'file_size_bytes',
    ];

    protected $casts = [
        'file_size_bytes' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id', 'book_id');
    }

    public function format()
    {
        return $this->belongsTo(Format::class, 'format_id', 'format_id');
    }

    public function getFileSizeInMbAttribute(): float
    {
        return round($this->file_size_bytes / 1048576, 2);
    }

    public function getFileExtensionAttribute(): string
    {
        return pathinfo($this->file_path, PATHINFO_EXTENSION);
    }

    public function getFileNameAttribute(): string
    {
        return basename($this->file_path);
    }
}
