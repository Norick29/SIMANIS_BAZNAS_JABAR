<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    protected $table = 'signatures';

    protected $primaryKey = 'id_signature';

    protected $fillable = [
        'id_aset',
        'id_pindah',
        'user_id',
        'signed_data',
        'signature',
        'public_key_fingerprint',
        'algorithm',
        'signed_at'
    ];

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id_user'
        );
    }
}
