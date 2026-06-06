<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Klien extends Model
{
    protected $table = 'klien';
    protected $primaryKey = 'id_klien';
    public $timestamps = true; // ✅ ubah false → true

    protected $fillable = [
        'id_user',
        'nama',
        'tanggal_lahir',
        'jenis_kelamin',
        'golongan_darah',
        'no_hp',
        'alamat',
        'institusi',
        'sosmed',
        'email',
        'domisili',
    ];
}