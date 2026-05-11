<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // 1. Tambahkan import ini

class User extends Authenticatable
{
    // 2. Tambahkan HasApiTokens di sini
    use HasApiTokens, Notifiable;

    protected $table = 'user'; 
    protected $primaryKey = 'id_user'; 
    public $timestamps = false; 

    protected $fillable = [
        'nama',
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];
}