<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    protected $table = 'divisis';
    protected $primaryKey = 'id_divisi';

    protected $fillable = [
        'nama_divisi',
    ];

    public function amil()
    {
        return $this->hasMany(Amil::class, 'id_divisi');
    }
}
