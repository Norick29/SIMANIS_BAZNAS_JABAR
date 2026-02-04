<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class sumber extends Model
{
    protected $table = 'sumbers'; // default plural Laravel
    protected $primaryKey = 'id_sumber_dana';

    protected $fillable = [
        'kode_sumber_dana',
        'nama_sumber_dana',
    ];
}
