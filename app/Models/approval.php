<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Approval extends Model
{
    use SoftDeletes;

    protected $table = 'approvals';
    protected $primaryKey = 'id_approval';
    protected $guarded = ['id_approval'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'approved_at'];
    protected $casts = [
        'id_approval' => 'integer',
        'id_pindah' => 'integer',
        'reference_id' => 'integer',
        'requested_by' => 'integer',
        'approved_by' => 'integer',
        'stage' => 'integer',
    ];

    public function pindahAset()
    {
        return $this->belongsTo(PindahAset::class, 'id_pindah', 'id_pindah');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by', 'id_user');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id_user');
    }

   
   public function reference()
    {
        return $this->morphTo('reference', 'approval_type', 'reference_id');
    }


}
