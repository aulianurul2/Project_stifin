<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'user'; // Nama tabel di database Anda
    protected $primaryKey = 'id_user'; // Primary key custom sesuai SQL
    public $timestamps = false; // Karena tabel SQL Anda tidak memiliki created_at/updated_at

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