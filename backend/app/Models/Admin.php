<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table      = 'admin';
    protected $primaryKey = 'id_admin';
    public    $timestamps = false;

    protected $fillable = [
        'id_user',
        'nama',
        'wa1',
        'wa2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}