<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\AsetHilang;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;


class AsetHilangController extends Controller
{
    public function index(Request $request)
{
    // 1. Siapkan daftar pilihan bulan dan tahun untuk dropdown filter
    $monthsList = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];

    // Mengambil rentang tahun dari data aset terkecil hingga tahun sekarang
    $currentYear = date('Y');
    $yearsList = range($currentYear, $currentYear - 5);

    // Ambil input filter atau set ke default tahun/bulan berjalan
    $selectedMonth = $request->get('bulan', date('m'));
    $selectedYear = $request->get('tahun', date('Y'));

    // 2. Bangun Query Utama
    // Kita filter di tabel induk 'assets' yang kondisinya 'hilang'
    $query = Asset::with(['barang', 'lokasi', 'asetHilangRecord'])
        ->where('kondisi', 'hilang');

    // Filter berdasarkan Bulan & Tahun Kehilangan (jika form berita acara sudah diisi)
    // atau berdasarkan created_at si aset jika belum diisi berita acaranya
    $query->where(function($q) use ($selectedMonth, $selectedYear) {
        $q->whereHas('asetHilangRecord', function($sub) use ($selectedMonth, $selectedYear) {
            $sub->whereMonth('tanggal_hilang', $selectedMonth)
                ->whereYear('tanggal_hilang', $selectedYear);
        })->orWhere(function($sub) use ($selectedMonth, $selectedYear) {
            $sub->whereDoesntHave('asetHilangRecord')
                ->whereMonth('created_at', $selectedMonth)
                ->whereYear('created_at', $selectedYear);
        });
    });

    // 🔍 FILTER STATUS PENGISIAN (Sudah diisi / Belum diisi)
    if ($request->filled('status_isi')) {
        if ($request->status_isi == 'selesai') {
            $query->has('asetHilangRecord');
        } elseif ($request->status_isi == 'belum') {
            $query->doesntHave('asetHilangRecord');
        }
    }

    // 🔍 FITUR SEARCH NAVBAR GLOBAL (Sinkron Pola dengan Maintenance)
    if ($request->filled('q')) {
        $search = $request->q;
        $query->where(function ($subQuery) use ($search) {
            $subQuery->where('kode_aset', 'like', '%' . $search . '%')
                ->orWhere('merk', 'like', '%' . $search . '%')
                ->orWhereHas('barang', function ($qBarang) use ($search) {
                    $qBarang->where('nama_barang', 'like', '%' . $search . '%');
                })
                ->orWhereHas('asetHilangRecord', function ($qRecord) use ($search) {
                    $qRecord->where('kronologi', 'like', '%' . $search . '%');
                });
        });
    }

    // Eksekusi menggunakan Paginate (misal 10 data per halaman)
    $assetsHilang = $query->paginate(10);

    return view('superadmin.aset.aset_hilang.index', compact(
        'assetsHilang', 
        'monthsList', 
        'yearsList', 
        'selectedMonth', 
        'selectedYear'
    ));
}

/**
     * Menyimpan form kronologi kehilangan (Mengunci data berita acara)
     */
    public function store(Request $request)
    {
        // 2. Cek kembali nama tabel aset Anda. Jika nama tabelnya 'aset', ubah menjadi 'exists:aset,id_aset'
        $request->validate([
            'id_aset'        => 'required|exists:assets,id_aset', 
            'tanggal_hilang' => 'required|date|before_or_equal:today',
            'kronologi'      => 'required|string|min:10',
        ]);

        $userId = Auth::check() ? (Auth::user()->id_user ?? Auth::id()) : null;

        if (!$userId) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Sesi Anda telah habis. Silakan login kembali.']);
        }

        try {
            // Simpan data
            AsetHilang::create([
                'id_aset'        => $request->id_aset,
                'tanggal_hilang' => $request->tanggal_hilang,
                'kronologi'      => $request->kronologi,
                'requested_by'   => $userId 
            ]);

            return redirect()
                ->route('hilang.index')
                ->with('success', 'Berita acara kronologi kehilangan berhasil disimpan.');

        } catch (\Exception $e) {
            // Jika ada error database tersembunyi, tangkap di sini agar tidak 'blank'
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    /**
     * Menampilkan detail kronologi kehilangan (Halaman Show)
     */
    public function showBeritaAcara($id)
{
    // Mengambil data rekam kehilangan beserta seluruh relasi terkait
    $hilang = AsetHilang::with([
        'asset.barang',
        'asset.lokasi',
        'asset.amil',
        'user'
    ])->findOrFail($id);

    return view('superadmin.aset.aset_hilang.berita-acara', compact('hilang'));
}

    /**
     * 🔄 FITUR ANTISIPASI: Jika Aset Ditemukan Kembali
     * Mengembalikan kondisi aset master ke 'baik' dan mengarsipkan kronologi via SoftDeletes
     */
    public function ditemukan(Request $request, $id_hilang)
{
    // Gunakan Database Transaction agar perubahan di 2 tabel (assets & aset_hilang) sinkron/aman
    DB::beginTransaction();

    try {
        // 1. Cari data rekam kehilangan asetnya
        $hilangRecord = AsetHilang::findOrFail($id_hilang);

        // 2. Ambil instansiasi data master aset terkait
        $asset = Asset::findOrFail($hilangRecord->id_aset);

        // 3. Kembalikan status kondisi aset master menjadi 'Baik' (atau sesuaikan dengan enum status di tokomu)
        $asset->update([
            'kondisi' => 'baik', // Menandakan barang siap dipakai operasional lagi
            'status'   => 'aktif' // Jika ada status operasional, misal: 'status_operasional' => 'Aktif'
        ]);

        // 4. Hapus data rekam hilang secara SoftDeletes agar hilang dari index komparasi audit aktif
        $hilangRecord->delete();

        // Jika semua proses query di atas sukses, kunci/simpan perubahan di database
        DB::commit();

        return redirect()->route('hilang.index')->with('success', 'Kondisi fisik aset ' . $asset->kode_aset . ' berhasil dipulihkan menjadi Baik.');

    } catch (\Exception $e) {
        // Jika ada query yang gagal di tengah jalan, batalkan semua perubahan data
        DB::rollBack();

        return redirect()->route('hilang.index')->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
    }
}

    public function downloadBeritaAcaraPdf($id)
{
    // Ambil data kronologi beserta relasi master aset, barang, lokasi awal, dan amil penanggung jawabnya
    $hilang = AsetHilang::with([
        'asset.barang',
        'asset.lokasi',
        'asset.amil', // Mengambil data amil penanggung jawab aset tersebut
        'user'        // Mengambil data user/staf yang menginput berita acara ini
    ])->findOrFail($id);

    // =====================================================
// GENERATE HASH SHA-256 DOKUMEN
// =====================================================

$documentHash = hash(
    'sha256',
    implode('|', [
        $hilang->id_hilang,
        $hilang->asset->kode_aset ?? '',
        $hilang->tanggal_hilang,
        $hilang->kronologi,
        $hilang->requested_by,
        $hilang->created_at
    ])
);

// =====================================================
// QR CODE PIHAK PELAPOR
// =====================================================

$qrDataPelapor = URL::signedRoute(
    'hilang.verify_public',
    [
        'id'     => $hilang->id_hilang,
        'action' => 'reported_by'
    ]
);

// =====================================================
// QR CODE AMIL PENANGGUNG JAWAB
// =====================================================

$qrDataAmil = null;

if ($hilang->asset && $hilang->asset->amil) {

    $qrDataAmil = URL::signedRoute(
        'hilang.verify_public',
        [
            'id'     => $hilang->id_hilang,
            'action' => 'responsible_amil'
        ]
    );
}
    // Load view khusus PDF berita acara hilang
    $pdf = Pdf::loadView('superadmin.aset.aset_hilang.berita-acara-pdf', [
        'hilang'        => $hilang,
        'qrDataPelapor' => $qrDataPelapor,
        'qrDataAmil'    => $qrDataAmil,
    ]);

    $pdf->setPaper('A4', 'portrait');
    
    // Opsi wajib DomPDF agar bisa membaca gambar/styling eksternal & inline PHP
    $pdf->setOption('isRemoteEnabled', true);
    $pdf->setOption('isPhpEnabled', true);

    // Format nama file PDF saat diunduh
    $namaFile = 'Berita_Acara_Kehilangan_' . str_replace('.', '_', $hilang->asset->kode_aset) . '_' . date('d-m-Y') . '.pdf';

    return $pdf->download($namaFile);
}


public function verifyPublic(Request $request, $id)
{
    $hilang = AsetHilang::with([
        'asset.barang',
        'asset.lokasi',
        'asset.amil',
        'user'
    ])->findOrFail($id);

    return view(
        'superadmin.aset.aset_hilang.verify_success',
        compact('hilang')
    );
}
}
