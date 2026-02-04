<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class asset extends Model
{
    use SoftDeletes;

    protected $table = 'assets';
    protected $primaryKey = 'id_aset';

    protected $fillable = [
        'kode_aset',
        'id_barang',
        'id_kategori',
        'id_jenis',
        'id_lokasi',
        'id_amil',
        'id_sumber_dana',
        'merk',
        'tahun',
        'volume',
        'satuan',
        'harga',
        'total_harga',
        'kondisi',
        'status',
        'qr_code',
    ];

     /**
     * =====================
     * RELATIONSHIP
     * =====================
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function jenis()
    {
        return $this->belongsTo(Jenis::class, 'id_jenis');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'id_lokasi');
    }

    public function amil()
    {
        return $this->belongsTo(Amil::class, 'id_amil');
    }

    public function sumberDana()
    {
        return $this->belongsTo(Sumber::class, 'id_sumber_dana');
    }

    /**
     * =====================
     * GENERATE KODE ASET
     * Format:
     * 22.04.41.47.A.20.028
     * =====================
     */
    public static function generateKodeAset($asset)
    {
        $tahun = substr($asset->tahun, -2);

        $kodeLokasi   = $asset->lokasi->kode_lokasi;
        $kodeKategori = $asset->kategori->kode_kategori;
        $kodeJenis    = $asset->jenis->kode_jenis;
        $kodeBarang   = $asset->barang->kode_barang;
        $kodeSumber   = $asset->sumberDana->kode_sumber;

        // hitung nomor urut
        $lastAsset = self::whereYear('created_at', $asset->tahun)
            ->orderBy('id_aset', 'desc')
            ->first();

        $urut = $lastAsset
            ? str_pad((int)substr($lastAsset->kode_aset, -3) + 1, 3, '0', STR_PAD_LEFT)
            : '001';

        return implode('.', [
            $kodeLokasi,
            $kodeKategori,
            $kodeJenis,
            $kodeBarang,
            $kodeSumber,
            $tahun,
            $urut
        ]);
    }
}
