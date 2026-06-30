<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterPenyusutan extends Model
{

    protected $table = 'master_penyusutan';

    protected $primaryKey = 'id_master_penyusutan';

    protected $fillable = [
        'id_jenis',
        'umur_ekonomis',
        'persentase',
        'status'
    ];

    public function jenis()
    {
        return $this->belongsTo(Jenis::class,'id_jenis');
    }
}
