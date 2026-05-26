<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barangs';
    protected $primaryKey = 'id_barang';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
    ];
}
