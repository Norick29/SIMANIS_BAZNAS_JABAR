<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategoris'; // default plural Laravel
    protected $primaryKey = 'id_kategori';

    protected $fillable = [
        'kode_kategori',
        'nama_kategori',
    ];
}
