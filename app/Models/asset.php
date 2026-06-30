<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
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

    public function asetHilangRecord()
    {
        return $this->hasOne(AsetHilang::class, 'id_aset', 'id_aset');
    }

    /**
     * Relasi ke riwayat/pengajuan penyusutan nilai aset (One-to-Many)
     * Dipakai jika satu aset bisa disusutkan berkali-kali sepanjang tahun
     */
    public function penyusutanRecords()
    {
        return $this->hasMany(PenyusutanAset::class, 'id_aset', 'id_aset');
    }

    /**
     * Relasi ke pengajuan penghapusan aset (One-to-One / One-to-Many)
     * Biasanya satu aset hanya diajukan hapus sekali
     */
    public function penghapusanRecord()
    {
        return $this->hasOne(PenghapusanAset::class, 'id_aset', 'id_aset');
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

    public function masterPenyusutan()
    {
        return $this->belongsTo(MasterPenyusutan::class, 'id_jenis', 'id_jenis'); 
    }

    public function getNilaiBukuAttribute()
    {
        if ($this->relationLoaded('penyusutanRecords')) {
            $penyusutanTerakhir = $this->penyusutanRecords
                ->filter(function ($record) {
                    return $record->status === 'disetujui'
                        && $record->requested_by === null;
                })
                ->sortByDesc(function ($record) {
                    return sprintf(
                        '%s-%010d',
                        optional($record->created_at)->format('YmdHis') ?? '',
                        $record->id_penyusutan
                    );
                })
                ->first();

            $nilaiDasar = $penyusutanTerakhir?->nilai_setelah_penyusutan ?? $this->total_harga ?? 0;

            $totalPemulihan = $this->penyusutanRecords
                ->filter(function ($record) {
                    return $record->status === 'disetujui'
                        && str_contains(strtolower($record->alasan ?? ''), 'peremajaan');
                })
                ->sum(function ($record) {
                    return (float) $record->nilai_penyusutan * 0.50;
                });

            return (float) $nilaiDasar + (float) $totalPemulihan;
        }

        $penyusutanTerakhir = $this->penyusutanRecords()
            ->where('status', 'disetujui')
            ->whereNull('requested_by')
            ->orderBy('created_at', 'desc')
            ->orderBy('id_penyusutan', 'desc')
            ->first();

        $nilaiDasar = $penyusutanTerakhir?->nilai_setelah_penyusutan ?? $this->total_harga ?? 0;

        $totalPemulihan = $this->penyusutanRecords()
            ->where('status', 'disetujui')
            ->where('alasan', 'LIKE', '%peremajaan%')
            ->get()
            ->sum(function ($record) {
                return (float) $record->nilai_penyusutan * 0.50;
            });

        return (float) $nilaiDasar + (float) $totalPemulihan;
    }

    public function getNilaiPerolehanAttribute()
    {
        if ($this->relationLoaded('penyusutanRecords')) {
            $nilaiAwalTercatat = $this->penyusutanRecords
                ->sortBy([
                    ['created_at', 'asc'],
                    ['id_penyusutan', 'asc'],
                ])
                ->first()
                ?->nilai_awal;

            return (float) ($nilaiAwalTercatat ?? $this->total_harga ?? 0);
        }

        $nilaiAwalTercatat = $this->penyusutanRecords()
            ->orderBy('created_at', 'asc')
            ->orderBy('id_penyusutan', 'asc')
            ->value('nilai_awal');

        return (float) ($nilaiAwalTercatat ?? $this->total_harga ?? 0);
    }

    public function getNilaiBukuPenghapusanAttribute()
    {
        if ($this->relationLoaded('penyusutanRecords')) {
            $nilaiDariUsulan = $this->penyusutanRecords
                ->filter(function ($record) {
                    return $record->status === 'disetujui'
                        && $record->requested_by !== null
                        && !str_contains(strtolower($record->alasan ?? ''), 'peremajaan');
                })
                ->sortBy([
                    ['created_at', 'desc'],
                    ['id_penyusutan', 'desc'],
                ])
                ->first()
                ?->nilai_setelah_penyusutan;

            if ($nilaiDariUsulan !== null) {
                return (float) $nilaiDariUsulan;
            }

            $nilaiDariSusutTerakhir = $this->penyusutanRecords
                ->whereNull('requested_by')
                ->sortBy([
                    ['created_at', 'desc'],
                    ['id_penyusutan', 'desc'],
                ])
                ->first()
                ?->nilai_setelah_penyusutan;

            return (float) ($nilaiDariSusutTerakhir ?? $this->total_harga ?? 0);
        }

        $nilaiDariUsulan = $this->penyusutanRecords()
            ->where('status', 'disetujui')
            ->whereNotNull('requested_by')
            ->where('alasan', 'NOT LIKE', '%peremajaan%')
            ->orderBy('created_at', 'desc')
            ->orderBy('id_penyusutan', 'desc')
            ->value('nilai_setelah_penyusutan');

        if ($nilaiDariUsulan !== null) {
            return (float) $nilaiDariUsulan;
        }

        $nilaiDariSusutTerakhir = $this->penyusutanRecords()
            ->whereNull('requested_by')
            ->orderBy('created_at', 'desc')
            ->orderBy('id_penyusutan', 'desc')
            ->value('nilai_setelah_penyusutan');

        return (float) ($nilaiDariSusutTerakhir ?? $this->total_harga ?? 0);
    }
}
