<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenyusutanAset extends Model
{
    use SoftDeletes;

    protected $table = 'penyusutan_aset';
    protected $primaryKey = 'id_penyusutan';
    protected $guarded = ['id_penyusutan'];

    // Relasi ke master Aset
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'id_aset', 'id_aset');
    }

    // Relasi ke User yang mengajukan
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by', 'id_user');
    }

    // Relasi Polymorphic ke tabel approvals
    public function approvals()
    {
        return $this->morphMany(Approval::class, 'reference', 'approval_type', 'reference_id', 'id_penyusutan');
    }
}
