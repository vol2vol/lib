<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory; // для генерации данных

    protected $primaryKey = 'role_id';
    public $timestamps = true;

    protected $fillable = [
        'role_name'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }
}
