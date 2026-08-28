<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PenghapusanAset;
use Illuminate\Support\Facades\URL;
use Barryvdh\DomPDF\Facade\Pdf;


class PenghapusanAsetController extends Controller
{
public function index(Request $request)
{
    // 1. Siapkan data Master Penunjang Filter
    $monthsList = [
        'ALL' => 'Semua Bulan', '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
        '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli',
        '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    
    // Ambil daftar tahun unik dari data penghapusan, jika kosong gunakan tahun sekarang
    $yearsList = \App\Models\PenghapusanAset::selectRaw('YEAR(created_at) as tahun')
        ->distinct()
        ->orderBy('tahun', 'desc')
        ->pluck('tahun')
        ->toArray();
        
    if (empty($yearsList)) {
        $yearsList = [date('Y')];
    }

    // Ambil master lokasi untuk opsi dropdown filter
    $lokasis = \App\Models\Lokasi::orderBy('nama_lokasi', 'asc')->get();

    // 2. Baca Request Parameter Filter (Set Default jika tidak diisi)
    $selectedMonth = $request->get('bulan', 'ALL');
    $selectedYear = $request->get('tahun', date('Y'));
    $statusFilter = $request->get('status');
    $lokasiFilter = $request->get('id_lokasi'); // Menggunakan lokasi asal aset

    // 3. Bangun Query Data Penghapusan
    $query = \App\Models\PenghapusanAset::with([
        'asset' => function($q) {
            $q->withTrashed()->with(['barang', 'lokasi']);
        },
        'requestedBy'
    ]);

    // Filter Berdasarkan Waktu Pembuatan Log
    if ($selectedMonth !== 'ALL') {
        $query->whereMonth('created_at', $selectedMonth);
    }
    $query->whereYear('created_at', $selectedYear);

    // Filter Berdasarkan Status Log
    if (!empty($statusFilter)) {
        $query->where('status', $statusFilter);
    }

    // Filter Berdasarkan Lokasi Terakhir Aset (Menggunakan WhereHas)
    if (!empty($lokasiFilter)) {
        $query->whereHas('asset', function($q) use ($lokasiFilter) {
            $q->withTrashed()->where('id_lokasi', $lokasiFilter);
        });
    }

    $penghapusans = $query->latest()->get();

    return view('superadmin.aset.penghapusan.index', compact(
        'penghapusans', 
        'monthsList', 
        'yearsList', 
        'lokasis',
        'selectedMonth', 
        'selectedYear'
    ));
}

public function show($id)
{
    $penghapusan = PenghapusanAset::with([
        'asset' => function($query) {
            $query->withTrashed()->with(['barang', 'lokasi', 'amil']);
        },
        'requestedBy'
    ])->findOrFail($id);

    return view('superadmin.aset.penghapusan.berita-acara', compact('penghapusan'));
}

public function cetakBeritaAcaraPdf($id)
{
    // Ambil data penghapusan beserta relasi asetnya (walaupun aset sudah di-soft delete)
    $penghapusan = \App\Models\PenghapusanAset::with([
        'asset' => function($query) {
            $query->withTrashed()->with(['barang', 'lokasi', 'amil']);
        },
        'requestedBy'
    ])->findOrFail($id);

    // Ambil data approval polimorfik yang berkaitan dengan data penghapusan ini
    $approvals = \App\Models\Approval::where('reference_id', $penghapusan->id_penghapusan)
        ->where('approval_type', \App\Models\PenghapusanAset::class)
        ->where('status', 'disetujui')
        ->with('approvedBy')
        ->get();

    $kadeptSignature = $approvals->where('stage', 1)->first();
    $kadivSignature = $approvals->where('stage', 2)->first();

    // Siapkan data penandatangan digital QR Code
    $qrDataKadept = null;
    if ($kadeptSignature) {
        $qrDataKadept = URL::signedRoute('approvals.verify', ['id' => $kadeptSignature->id_approval]);
    }

    $qrDataKadiv = null;
    if ($kadivSignature) {
        $qrDataKadiv = URL::signedRoute('approvals.verify', ['id' => $kadivSignature->id_approval]);
    }

    $pdf = Pdf::loadView('superadmin.aset.penghapusan.berita-acara-pdf', [
        'penghapusan'     => $penghapusan,
        'kadeptSignature' => $kadeptSignature,
        'kadivSignature'  => $kadivSignature,
        'qrDataKadept'    => $qrDataKadept,
        'qrDataKadiv'     => $qrDataKadiv,
    ]);

    $pdf->setPaper('A4', 'portrait');
    $pdf->setOption('isRemoteEnabled', true);
    $pdf->setOption('isPhpEnabled', true);

    return $pdf->download('Berita_Acara_Penghapusan_' . ($penghapusan->asset->kode_aset ?? 'Aset') . '_' . now()->format('d-m-Y') . '.pdf');
}

public function printLaporanPenghapusan(Request $request)
{
    // 1. Logika Query & Filtering data Penghapusan
    $query = \App\Models\PenghapusanAset::with([
        'asset' => function ($q) {
            $q->withTrashed()->with(['barang', 'penyusutanRecords']);
        },
        'requestedBy'
    ]);

    if ($request->filled('id_lokasi')) {
        $query->whereHas('asset', function($q) use ($request) {
            $q->withTrashed()->where('id_lokasi', $request->id_lokasi);
        });
    }

    // 🔑 NEW FILTER LOGIC: Jenis barang apa yang paling sering dihapus/dimusnahkan
    if ($request->filled('id_jenis')) {
        $query->whereHas('asset', function($q) use ($request) {
            $q->withTrashed()->where('id_jenis', $request->id_jenis);
        });
    }

    $dataPenghapusan = $query->orderBy('created_at', 'desc')->get();
    
    // Hitung taksiran nilai buku aset yang dihapus untuk manifes
    $totalNilaiBukuDihapus = $dataPenghapusan->sum(function($item) {
        return $item->asset ? $item->asset->nilai_buku_penghapusan : 0;
    });

    // Metadata Audit Cetak
    $printedBy = $request->user()->name;
    $role      = $request->user()->role;
    $timestamp = now()->format('d M Y, H:i:s');

    // 2. Ambil Data User Pimpinan Aktif
    $userKadept = \App\Models\User::where('role', 'kadept')->first();
    $userKadiv  = \App\Models\User::where('role', 'kadiv')->first();

    $kepalaDept   = $userKadept->name ?? '........................................';
    $kepalaDivisi = $userKadiv->name ?? '........................................';

    // 3. 🔑 DIGITAL SIGNATURE HMACS-SHA256 BERBASIS DATA MANIFES DISPOSAL CONTROL
    $fingerprintKadept = $userKadept ? substr(md5($userKadept->id_user . 'kadept'), 0, 12) : 'KADEPT-EMPTY';
    $fingerprintKadiv  = $userKadiv ? substr(md5($userKadiv->id_user . 'kadiv'), 0, 12) : 'KADIV-EMPTY';

    $manifestKadept = "BAZNAS JABAR | Jenis: Laporan Penghapusan Aset | Oleh Kadept: {$kepalaDept} | Waktu: {$timestamp} | Est Nilai: Rp " . number_format($totalNilaiBukuDihapus, 0, ',', '.');
    $sigKadept = base64_encode(hash_hmac('sha256', $manifestKadept, 'SECRET_KEY_SYSTEM_BAZNAS_JABAR'));

    $manifestKadiv = "BAZNAS JABAR | Jenis: Laporan Penghapusan Aset | Oleh Kadiv: {$kepalaDivisi} | Waktu: {$timestamp} | Est Nilai: Rp " . number_format($totalNilaiBukuDihapus, 0, ',', '.');
    $sigKadiv = base64_encode(hash_hmac('sha256', $manifestKadiv, 'SECRET_KEY_SYSTEM_BAZNAS_JABAR'));

    $qrDataKadept = URL::signedRoute('reports.verify', [
        'document' => 'Laporan Penghapusan Aset',
        'role' => 'Kepala Departemen',
        'signer' => $kepalaDept,
        'printed_by' => $printedBy,
        'printed_at' => $timestamp,
        'fingerprint' => 'KEY-' . $fingerprintKadept,
        'signature' => substr($sigKadept, 0, 25) . '...',
    ]);
    $qrDataKadiv  = URL::signedRoute('reports.verify', [
        'document' => 'Laporan Penghapusan Aset',
        'role' => 'Kepala Divisi',
        'signer' => $kepalaDivisi,
        'printed_by' => $printedBy,
        'printed_at' => $timestamp,
        'fingerprint' => 'KEY-' . $fingerprintKadiv,
        'signature' => substr($sigKadiv, 0, 25) . '...',
    ]);

    $qrCodeSystem = "SIMASET DISPOSAL STAMP | Operator Cetak: {$printedBy} ({$role}) | Waktu: {$timestamp} | Status Data: Valid.";

    // 4. Kirim Notifikasi Pengawasan
    try {
        if ($userKadiv) {
            $userKadiv->notify(new \App\Notifications\AssetMutationNotification([
                'id_pindah' => null,
                'title'     => 'Log Pengawasan: Cetak Laporan Penghapusan',
                'message'   => "User '{$printedBy}' ({$role}) baru saja mendownload Laporan Rekapitulasi Penghapusan Aset.",
                'icon'      => 'fas fa-print',
                'bg_color'  => 'bg-info',
                'url'       => url('/laporan-aset')
            ]));
        }
        if ($userKadept) {
            $userKadept->notify(new \App\Notifications\AssetMutationNotification([
                'id_pindah' => null,
                'title'     => 'Log Pengawasan: Cetak Laporan Penghapusan',
                'message'   => "User '{$printedBy}' ({$role}) baru saja mendownload Laporan Rekapitulasi Penghapusan Aset.",
                'icon'      => 'fas fa-print',
                'bg_color'  => 'bg-info',
                'url'       => url('/laporan-aset')
            ]));
        }
    } catch (\Exception $eMail) { }

    return view('superadmin.reports.print_penghapusan', compact(
        'dataPenghapusan', 'qrDataKadept', 'qrDataKadiv', 'qrCodeSystem', 
        'printedBy', 'timestamp', 'kepalaDivisi', 'kepalaDept'
    ));
}
}
