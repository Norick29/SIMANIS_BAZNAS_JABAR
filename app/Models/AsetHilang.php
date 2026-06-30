<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AsetHilang extends Model
{
    use SoftDeletes;

    protected $table = 'aset_hilang';
    protected $primaryKey = 'id_hilang';
    
    // Cukup gunakan fillable saja untuk mass assignment
    protected $fillable = [
        'id_aset',
        'tanggal_hilang',
        'kronologi',
        'requested_by'
    ];

    protected $casts = [
        'tanggal_hilang' => 'date',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'id_aset', 'id_aset');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by', 'id_user');
    }

    public function user()
{
    return $this->belongsTo(User::class, 'requested_by', 'id_user');
}
}
