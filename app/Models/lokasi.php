<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class lokasi extends Model
{
    protected $table = 'lokasis';
    protected $primaryKey = 'id_lokasi';

    protected $fillable = [
        'kode_lokasi',
        'nama_lokasi',
    ];
}
