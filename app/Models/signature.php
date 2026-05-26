<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    protected $table = 'signatures';
    protected $primaryKey = 'id_signature';
    protected $guarded = ['id_signature'];
    protected $dates = ['created_at', 'updated_at', 'signed_at'];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'id_aset', 'id_aset');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
