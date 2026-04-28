<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Klien extends Model
{
    protected $table = 'klien';
    protected $primaryKey = 'id_klien';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'nama',
        'no_hp',
        'tanggal_lahir',
        'alamat',
        'jenis_kelamin',
    ];
}