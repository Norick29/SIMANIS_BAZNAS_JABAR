<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class divisi extends Model
{
    protected $table = 'divisis';
    protected $primaryKey = 'id_divisi';

    protected $fillable = [
        'nama_divisi',
    ];

    // public function amil()
    // {
    //     return $this->belongsTo(amil::class, 'id_amil');
    // }
}
