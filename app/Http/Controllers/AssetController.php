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
use App\Models\Approval;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
        $lastAset = Asset::where('id_lokasi', $request->id_lokasi)
            ->where('id_kategori', $request->id_kategori)
            ->orderBy('id_aset', 'desc') // ambil terakhir
            ->first();

        if ($lastAset) {
            // ambil 3 digit terakhir dari kode_aset
            $lastUrut = (int) substr($lastAset->kode_aset, -3);
            $nextUrut = $lastUrut + 1;
        } else {
            $nextUrut = 1;
        }

        $urut = str_pad($nextUrut, 3, '0', STR_PAD_LEFT);

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
        $signedUrl = URL::signedRoute('assets.scan', [
            'kode_aset' => $kodeAset
        ]);

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($signedUrl)
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

public function show($id)
{
    $aset = Asset::with([
        'barang',
        'kategori',
        'jenis',
        'lokasi',
        'amil',
        'sumberDana'
    ])->findOrFail($id);

    return response()->json([
        'id_aset'      => $aset->id_aset,
        'kode_aset'    => $aset->kode_aset,
        'qr_code'      => $aset->qr_code ? asset($aset->qr_code) : null,
        'barang'       => $aset->barang->nama_barang ?? '-',
        'merk'         => $aset->merk ?? '-',
        'tahun'        => $aset->tahun ?? '-',
        'volume'       => $aset->volume ?? '-',
        'satuan'       => $aset->satuan ?? '-',
        'kondisi'      => $aset->kondisi ?? '-',
        'status'       => $aset->status,
        'harga'        => $aset->harga ?? '-',
        'total_harga'  => $aset->total_harga ?? '-',
        'kategori'     => $aset->kategori->nama_kategori ?? '-',
        'jenis'        => $aset->jenis->nama_jenis ?? '-',
        'lokasi'       => $aset->lokasi->nama_lokasi ?? '-',
        'amil'         => $aset->amil->nama ?? '-',
        'sumber_dana'  => $aset->sumberDana->nama_sumber_dana ?? '-',
    ]);
}

public function showFormPindah($id_aset)
{
    $aset = Asset::with([
        'barang',
        'lokasi',
        'amil',
    ])->findOrFail($id_aset);

    return view('superadmin.aset.data_aset.form-pindah', compact('aset'));
}

public function verifyLoginPindah(Request $request)
{
    $request->validate([
        'password' => 'required',
    ]);

    // if (!Auth::attempt(['email' => Auth::user()->email, 'password' => $request->password])) {
    //     return back()->with('error', 'Password salah, verifikasi gagal.');
    // }
    if (!Hash::check($request->password, Auth::user()->password)) {
        return back()->with('error', 'Password salah, verifikasi gagal.');
    }

    return response()->json([
        'success' => true,
        'message' => 'Verifikasi berhasil'
    ]);
}

public function getReferensiData()
{
    return response()->json([
        'barangs'   => Barang::orderBy('nama_barang')->get(),
        'kategoris' => Kategori::orderBy('nama_kategori')->get(),
        'jenis'     => Jenis::orderBy('nama_jenis')->get(),
        'lokasis'   => Lokasi::orderBy('nama_lokasi')->get(),
        'amils'     => Amil::orderBy('nama')->get(),
        'sumbers'   => Sumber::orderBy('kode_sumber_dana')->get(),
    ]);
}

public function getReferensiPindah()
{
    return response()->json([
        'amils' => Amil::orderBy('nama')->get(),
        'lokasis' => Lokasi::orderBy('nama_lokasi')->get(),
    ]);
}

public function pindahAset(Request $request)
{
    $request->validate([
        'id_aset' => 'required|exists:assets,id_aset',
        'id_amil_sesudah' => 'required|exists:amils,id_amil',
        'id_lokasi_baru' => 'required|exists:lokasis,id_lokasi',
        'alasan' => 'required|string|max:500',
    ]);

    DB::beginTransaction();

    try {

        $aset = Asset::findOrFail($request->id_aset);

        // VALIDASI LOGIC

        if ($aset->id_amil == $request->id_amil_sesudah) {
            throw new \Exception('Penanggung jawab baru tidak boleh sama.');
        }

        if ($aset->id_lokasi == $request->id_lokasi_baru) {
            throw new \Exception('Lokasi baru tidak boleh sama.');
        }

        // CEK PENDING

        $pending = PindahAset::where('id_aset', $aset->id_aset)
            ->where('status', 'menunggu')
            ->exists();

        if ($pending) {
            throw new \Exception('Aset masih memiliki permintaan perpindahan yang pending.');
        }

        // SIMPAN KE TABEL PINDAH_ASETS
        $pindah = PindahAset::create([
            'id_aset' => $aset->id_aset,
            'id_amil_sebelum' => $aset->id_amil,
            'id_amil_sesudah' => $request->id_amil_sesudah,
            'id_lokasi_baru' => $request->id_lokasi_baru,
            'alasan' => $request->alasan,
            'status' => 'menunggu',
            'requested_by' => Auth::id(),
        ]);

        // BUAT APPROVAL RECORD - STAGE 1 (KADEPT)
        $approval = Approval::create([
            'id_pindah' => $pindah->id_pindah,
            'stage' => 1, // Stage 1 untuk Kepala Departemen
            'status' => 'menunggu',
            'approved_by' => null, // Belum ada yang approve
            'approval_date' => null,
        ]);

        // JIKA BUTUH STAGE 2, BUAT APPROVAL KEDUA
        // Biarkan stage 1 selesai dulu, nanti stage 2 dibuat otomatis

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Permintaan pindah aset berhasil dikirim. Menunggu approval dari Kepala Departemen.'
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

public function scanQr($kode_aset)
{
    $aset = Asset::where('kode_aset', $kode_aset)
        ->with([
            'barang',
            'kategori',
            'jenis',
            'lokasi',
            'amil',
            'sumberDana'
        ])
        ->firstOrFail();

    // Return halaman dengan modal detail yang langsung terbuka
    return view('superadmin.aset.data_aset.scan-result', compact('aset'));
}

public function update(Request $request, $id)
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

    $aset = Asset::findOrFail($id);

    $totalHarga = $request->volume * $request->harga;

    $aset->update([
        'id_barang'        => $request->id_barang,
        'id_kategori'      => $request->id_kategori,
        'id_jenis'         => $request->id_jenis,
        'id_lokasi'        => $request->id_lokasi,
        'id_amil'          => $request->id_amil,
        'id_sumber_dana'   => $request->id_sumber_dana,
        'merk'             => $request->merk,
        'tahun'            => $request->tahun,
        'volume'           => $request->volume,
        'satuan'           => $request->satuan,
        'harga'            => $request->harga,
        'total_harga'      => $totalHarga,
        'kondisi'          => $request->kondisi,
        'status'           => $request->status,
    ]);

    return redirect()
        ->route('assets.index')
        ->with('success', 'Data aset berhasil diperbarui.');
}

public function destroy($id)
{
    $aset = Asset::findOrFail($id);
    $aset->delete();

    return redirect()
        ->back()
        ->with('success', 'Data aset berhasil dihapus.');
}
}
