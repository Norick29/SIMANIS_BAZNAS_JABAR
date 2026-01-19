<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class jabatan extends Model
{
    protected $table = 'jabatans';
    protected $primaryKey = 'id_jabatan';

    protected $fillable = [
        'nama_jabatan',
    ];

    // public function amil()
    // {
    //     return $this->belongsTo(amil::class, 'id_amil');
    // }
}
