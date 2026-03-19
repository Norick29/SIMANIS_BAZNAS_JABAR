<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Jenis;
use App\Models\Lokasi;
use App\Models\Amil;
use App\Models\Sumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use App\Models\PindahAset;

class AssetController extends Controller
{
    

public function index()
{
    $assets   = Asset::latest()->paginate(10);
    $barangs  = Barang::orderBy('nama_barang')->get();
    $kategoris = Kategori::orderBy('nama_kategori')->get();
    $jenis    = Jenis::orderBy('nama_jenis')->get();
    $lokasis  = Lokasi::orderBy('nama_lokasi')->get();
    $amils    = Amil::orderBy('nama')->get();
    $sumbers  = Sumber::orderBy('kode_sumber_dana')->get();

    return view('superadmin.aset.data_aset.index', compact(
        'assets',
        'barangs',
        'kategoris',
        'jenis',
        'lokasis',
        'amils',
        'sumbers'
    ));
} 


public function store(Request $request)
{
    $request->validate([
        'id_barang'        => 'required|exists:barangs,id_barang',
        'id_kategori'      => 'required|exists:kategoris,id_kategori',
        'id_jenis'         => 'required|exists:jenis,id_jenis',
        'id_lokasi'        => 'required|exists:lokasis,id_lokasi',
        'id_amil'          => 'required|exists:amils,id_amil',
        'id_sumber_dana'   => 'required|exists:sumbers,id_sumber_dana',
        'merk'             => 'nullable|string|max:100',
        'tahun'            => 'required|digits:4',
        'volume'           => 'required|integer|min:1',
        'satuan'           => 'nullable|string|max:20',
        'harga'            => 'required|numeric|min:0',
        'kondisi'          => 'required|in:baik,rusak,hilang',
        'status'           => 'required|in:aktif,nonaktif',
    ]);

    DB::beginTransaction();

    try {

        /* ==========================
         * 1. CEK DUPLIKAT DATA ASET
         * ========================== */
        $duplikat = Asset::where('id_barang', $request->id_barang)
            ->where('id_lokasi', $request->id_lokasi)
            ->where('tahun', $request->tahun)
            ->where('id_sumber_dana', $request->id_sumber_dana)
            ->whereNull('deleted_at')
            ->exists();

        if ($duplikat) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Data aset sudah terdaftar sebelumnya.');
        }

        /* ==========================
         * 2. GENERATE KODE ASET FIX
         * ========================== */

        // Ambil data master
        $barang   = Barang::findOrFail($request->id_barang);
        $kategori = Kategori::findOrFail($request->id_kategori);
        $jenis    = Jenis::findOrFail($request->id_jenis);
        $lokasi   = Lokasi::findOrFail($request->id_lokasi);
        $sumber   = Sumber::findOrFail($request->id_sumber_dana);

        // Ambil KODE BUKAN ID
        $kodeLokasi   = str_pad($lokasi->kode_lokasi, 2, '0', STR_PAD_LEFT);
        $kodeKategori = str_pad($kategori->kode_kategori, 2, '0', STR_PAD_LEFT);
        $kodeJenis    = str_pad($jenis->kode_jenis, 2, '0', STR_PAD_LEFT);
        $kodeBarang   = str_pad($barang->kode_barang, 2, '0', STR_PAD_LEFT);

        // Sumber dana (pakai kode, bukan asumsi A/B/C)
        $kodeSumber = $sumber->kode_sumber_dana;

        // Tahun pembelian
        $tahun = substr($request->tahun, 2, 2);

        // Nomor urut
        $lastUrut = Asset::whereYear('created_at', $request->tahun)->count() + 1;
        $urut = str_pad($lastUrut, 3, '0', STR_PAD_LEFT);

        // Final kode aset
        $kodeAset = "{$kodeLokasi}.{$kodeKategori}.{$kodeJenis}.{$kodeBarang}.{$kodeSumber}.{$tahun}.{$urut}";


        /* ==========================
         * 3. HITUNG TOTAL HARGA
         * ========================== */
        $totalHarga = $request->volume * $request->harga;

        /* ==========================
         * 4. GENERATE QR CODE
         * ========================== */
        $qrDirectory = public_path('qr-assets');

        if (!file_exists($qrDirectory)) {
            mkdir($qrDirectory, 0755, true);
        }
        
        $qrFileName = 'qr_' . Str::uuid() . '.png';
        $qrPath     = 'qr-assets/' . $qrFileName;

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($kodeAset)
            ->size(300)
            ->margin(5)
            ->build();

        $result->saveToFile(public_path($qrPath));

        /* ==========================
         * 5. SIMPAN DATA
         * ========================== */
        Asset::create([
            'kode_aset'      => $kodeAset,
            'id_barang'      => $request->id_barang,
            'id_kategori'    => $request->id_kategori,
            'id_jenis'       => $request->id_jenis,
            'id_lokasi'      => $request->id_lokasi,
            'id_amil'        => $request->id_amil,
            'id_sumber_dana' => $request->id_sumber_dana,
            'merk'           => $request->merk,
            'tahun'          => $request->tahun,
            'volume'         => $request->volume,
            'satuan'         => $request->satuan,
            'harga'          => $request->harga,
            'total_harga'    => $totalHarga,
            'kondisi'        => $request->kondisi,
            'status'         => $request->status,
            'qr_code'        => $qrPath,
        ]);

        DB::commit();

        return redirect()
            ->route('assets.index')
            ->with('success', 'Data aset berhasil ditambahkan.');

    } catch (\Exception $e) {

        DB::rollBack();

        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'Gagal menyimpan data aset: ' . $e->getMessage());
    }
}

public function pindahAset(Request $request)
{
    $request->validate([
        'id_aset'          => 'required|exists:assets,id_aset',
        'id_amil_sesudah'  => 'required|exists:amils,id_amil',
        'id_lokasi_baru'   => 'required|exists:lokasis,id_lokasi',
        'alasan'           => 'nullable|string|max:255',
    ]);

    DB::beginTransaction();

    try {

        $aset = Asset::findOrFail($request->id_aset);

        // simpan riwayat pindah
        PindahAset::create([
            'id_aset'         => $aset->id_aset,
            'id_amil_sebelum' => $aset->id_amil,
            'id_amil_sesudah' => $request->id_amil_sesudah,
            'id_lokasi_baru'  => $request->id_lokasi_baru,
            'alasan'          => $request->alasan,
            'status'          => 'selesai',
        ]);

        // update aset utama
        $aset->update([
            'id_amil'   => $request->id_amil_sesudah,
            'id_lokasi' => $request->id_lokasi_baru,
        ]);

        DB::commit();

        return redirect()
            ->route('assets.index')
            ->with('success', 'Aset berhasil dipindahkan.');

    } catch (\Exception $e) {

        DB::rollBack();

        return redirect()
            ->back()
            ->with('error', 'Gagal memindahkan aset: ' . $e->getMessage());
    }
}


}
