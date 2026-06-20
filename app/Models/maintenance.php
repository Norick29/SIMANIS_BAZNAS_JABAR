<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maintenance extends Model
{
    use SoftDeletes;

    protected $table = 'maintenances'; // Sesuaikan jika nama tabelmu berbeda

    protected $primaryKey = 'id_maintenance';

    protected $fillable = [
        'id_aset',
        'jenis',
        'deskripsi',
        'durasi',
        'biaya',
        'status',
        'tanggal_mulai',
        'tanggal_selesai'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'biaya' => 'decimal:2'
    ];

    // Relasi balik ke Asset
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'id_aset', 'id_aset');
    }
}
