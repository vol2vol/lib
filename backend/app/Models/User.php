<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory;
    
    use HasApiTokens;

    protected $primaryKey = 'user_id';
    public $timestamps = true;

    protected $fillable = [
        'login',
        'password',
        'role_id'
    ];

    protected $hidden = [
        'password'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function favoriteBooks()
    {
        return $this->belongsToMany(Book::class, 'favorite_books', 'user_id', 'book_id');
    }
}
