<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Amil extends Model
{
    use HasFactory;

    protected $table = 'amils';
    protected $primaryKey = 'id_amil';

    protected $fillable = [
        'nia',
        'nama',
        'id_jabatan',
        'id_divisi',
        'nama_dept',
    ];

    /** Relasi ke Jabatan */
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan');
    }

    /** Relasi ke Divisi */
    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi');
    }
}
