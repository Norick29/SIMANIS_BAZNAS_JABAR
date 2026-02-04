<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class jenis extends Model
{
    protected $table = 'jenis';
    protected $primaryKey = 'id_jenis';

    protected $fillable = [
        'kode_jenis',
        'nama_jenis'
    ];
}
