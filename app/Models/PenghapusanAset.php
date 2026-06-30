<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenghapusanAset extends Model
{
    use SoftDeletes;

    protected $table = 'penghapusan_aset';
    protected $primaryKey = 'id_penghapusan';
    protected $guarded = ['id_penghapusan'];

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
        return $this->morphMany(Approval::class, 'reference', 'approval_type', 'reference_id');
    }
}
