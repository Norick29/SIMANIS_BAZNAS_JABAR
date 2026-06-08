<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PindahAset extends Model
{
    use SoftDeletes;

    protected $table = 'pindah_asets';
    protected $primaryKey = 'id_pindah';
    protected $guarded = ['id_pindah'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * =====================
     * RELATIONSHIP
     * =====================
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'id_aset', 'id_aset');
    }

    public function amilSebelum()
    {
        return $this->belongsTo(Amil::class, 'id_amil_sebelum', 'id_amil');
    }

    public function amilSesudah()
    {
        return $this->belongsTo(Amil::class, 'id_amil_sesudah', 'id_amil');
    }

    public function lokasiBaru()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi_baru', 'id_lokasi');
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class, 'id_pindah', 'id_pindah');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by', 'id_user');
    }

    public function signatures()
    {
        return $this->hasMany(
            Signature::class,
            'id_pindah',
            'id_pindah'
        );
    }
}
