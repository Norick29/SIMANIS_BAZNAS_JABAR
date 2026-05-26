<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Jabatan extends Model
{
    protected $table = 'jabatans';
    protected $primaryKey = 'id_jabatan';

    protected $fillable = [
        'nama_jabatan',
    ];

    public function amil()
    {
        return $this->hasMany(Amil::class, 'id_jabatan');
    }

}
