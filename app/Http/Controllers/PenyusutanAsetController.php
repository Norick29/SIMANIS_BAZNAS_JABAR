<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PenyusutanAset;
use App\Models\PenghapusanAset;
use App\Models\Asset;
use App\Models\Approval;
use App\Models\MasterPenyusutan;
use App\Models\User;
use App\Notifications\AssetMutationNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use Barryvdh\DomPDF\Facade\Pdf;

class PenyusutanAsetController extends Controller
{
   public function index(Request $request)
{
    $tahunSekarang = (int)date('Y'); // Tahun berjalan saat ini: 2026

    // =========================================================================
// ⚡ ENGINE AUTOMATION PENYUSUTAN BERDASARKAN MASTER PENYUSUTAN
// =========================================================================

$assetsAktif = Asset::with([
    'barang',
    'jenis',
    'penyusutanRecords'
])
    ->where('status', 'aktif')
    ->where('kondisi', '!=', 'hilang')
    ->where('tahun', '<', $tahunSekarang)
    ->get();

foreach ($assetsAktif as $aset) {

    // Lewati apabila asset belum memiliki relasi barang / jenis
    if (!$aset->jenis) {
        continue;
    }

    // Ambil kebijakan penyusutan berdasarkan jenis aset
    $setting = MasterPenyusutan::where('id_jenis', $aset->id_jenis)
        ->where('status', 'aktif')
        ->first();

    // Jika jenis aset belum memiliki master penyusutan, lewati
    if (!$setting) {
        continue;
    }

    $umurAset = $tahunSekarang - (int)$aset->tahun;

    // Cek apakah tahun ini sudah pernah dibuat penyusutannya
    $sudahTercatatTahunIni = PenyusutanAset::where('id_aset', $aset->id_aset)
        ->whereYear('created_at', $tahunSekarang)
        ->exists();

    if (!$sudahTercatatTahunIni && $umurAset > 0) {

        DB::transaction(function () use ($aset, $setting, $umurAset) {

            $nilaiPerolehanAsli = (float) (
                PenyusutanAset::where('id_aset', $aset->id_aset)
                    ->orderBy('created_at', 'asc')
                    ->orderBy('id_penyusutan', 'asc')
                    ->value('nilai_awal')
                ?? $aset->total_harga
                ?? 0
            );

            if ($nilaiPerolehanAsli <= 0) {
                return;
            }

            $nilaiBukuSebelumSusut = (float) $aset->nilai_buku;
            $nilaiResiduMinimumTotal = $nilaiPerolehanAsli * 0.10;
            $persentaseTahunan = (float) $setting->persentase / 100;
            $penyusutanTahunIni = $nilaiPerolehanAsli * $persentaseTahunan;
            $ruangPenyusutan = max($nilaiBukuSebelumSusut - $nilaiResiduMinimumTotal, 0);
            $nilaiPenyusutanFinal = min($penyusutanTahunIni, $ruangPenyusutan);

            if ($nilaiPenyusutanFinal <= 0) {
                return;
            }

            $totalNilaiSetelah = max(
                $nilaiBukuSebelumSusut - $nilaiPenyusutanFinal,
                $nilaiResiduMinimumTotal
            );

            $catatanAlasan = "Penyusutan otomatis tahun {$umurAset} berdasarkan kebijakan master penyusutan ({$setting->persentase}% per tahun) dengan nilai residu minimum 10% dari nilai perolehan asli.";

            PenyusutanAset::create([

                'id_aset'                  => $aset->id_aset,
                'nilai_awal'               => $nilaiBukuSebelumSusut,
                'nilai_penyusutan'         => $nilaiPenyusutanFinal,
                'nilai_setelah_penyusutan' => $totalNilaiSetelah,
                'alasan'                   => $catatanAlasan,
                'status'                   => 'menunggu',
                'requested_by'             => null,

            ]);
        });
    }
}
    // =========================================================================
    // 🔍 RENDER VIEW DATA & HANDLING FILTER LIFECYCLE BARU
    // =========================================================================
    $query = PenyusutanAset::select('penyusutan_aset.*')
        ->join('assets', 'penyusutan_aset.id_aset', '=', 'assets.id_aset')
        ->with([
            'asset' => function($q) { $q->withTrashed(); },
            'asset.barang', 
            'asset.jenis.masterPenyusutan', // ⚡ TAMBAHKAN INI: Agar relasi master penyusutan ke-load dan tidak null!
            'requestedBy'
        ]);

    // ===============================================================
    // FILTER STATUS APPROVAL
    // ===============================================================
    if ($request->filled('status')) {
        $query->where(
            'penyusutan_aset.status',
            $request->status
        );
    }

    // ===============================================================
    // FILTER JENIS TINDAKAN
    // ===============================================================
    if ($request->filled('tindakan')) {
        switch ($request->tindakan) {
            case 'murni_susut':
                $query->whereNull('penyusutan_aset.requested_by');
            break;

            case 'peremajaan':
                $query->whereNotNull('penyusutan_aset.requested_by')
                      ->where('penyusutan_aset.alasan','LIKE','%peremajaan%');
            break;

            case 'penghapusan':
                $query->whereNotNull('penyusutan_aset.requested_by')
                      ->where('penyusutan_aset.alasan','LIKE','%penghapusan%');
            break;
        }
    }

    // ===============================================================
    // SORTING
    // ===============================================================
    $query->orderBy('assets.tahun','asc')
          ->orderBy('penyusutan_aset.created_at','desc');


    // ===============================================================
    // AMBIL DATA
    // ===============================================================
    $riwayat = $query->get();


    // ===============================================================
    // FILTER KEKRITISAN (DI-UPDATE BIAR SYNCHRONOUS SAMA TAHUN REALTIME)
    // ===============================================================
    if ($request->filled('kekritisan')) {
        $riwayat = $riwayat->filter(function($item) {
            if(!$item->asset || !$item->asset->jenis){
                return false;
            }

            // Sekarang ini dijamin tidak null karena sudah di-eager load di atas
            $master = $item->asset->jenis->masterPenyusutan;

            if(!$master){
                return false;
            }

            // Ambil selisih tahun secara dinamis
            $umurAset = (int)date('Y') - (int)$item->asset->tahun;

            if(request('kekritisan') == 'kritis'){
                return $umurAset >= $master->umur_ekonomis;
            }

            if(request('kekritisan') == 'optimal'){
                return $umurAset < $master->umur_ekonomis;
            }

            return true;
        });
    }

    // ===============================================================
    // PAGINATION COLLECTION (Tetap pakai gaya lama kamu)
    // ===============================================================
    $page = request()->get('page',1);
    $perPage = 10;

    // Tambahkan ->values() setelah filter agar index array collection di-reset (menghindari bug skip data di page 2)
    $slicedItems = $riwayat->forPage($page, $perPage)->values();

    $riwayat = new \Illuminate\Pagination\LengthAwarePaginator(
        $slicedItems,
        $riwayat->count(), // Total item dihitung dari keseluruhan data setelah di-filter kekritisan
        $perPage,
        $page,
        [
            'path'=>request()->url(),
            'query'=>request()->query()
        ]
    );
    return view('superadmin.aset.penyusutan.index', compact(
        'riwayat', 'tahunSekarang'
    ));
}

    public function ajukanPeremajaan(Request $request, $id_penyusutan)
    {
        if (in_array(Auth::user()->role, ['kadept', 'kadiv'])) {
            return redirect()->back()->with('error', 'Kepala departemen dan kepala divisi hanya dapat melakukan monitoring penyusutan.');
        }

        // 1. Validasi mengambil input name 'alasan'
        $request->validate([
            'alasan' => 'required|string|max:1000'
        ]);

        $penyusutan = PenyusutanAset::findOrFail($id_penyusutan);

        DB::beginTransaction();
        try {
            // Susun teks note indikator secara konsisten
            $teksUsulan = "USULAN PEREMAJAAN: " . $request->alasan;

            // 2. Pasang pengajuan approval stage 1 (Kadept)
            Approval::create([
                'approval_type' => PenyusutanAset::class,
                'reference_id'  => $penyusutan->id_penyusutan,
                'stage'         => 1,
                'status'        => 'menunggu',
                'requested_by'  => Auth::id(),
                'note'          => $teksUsulan // 🔑 Sudah diperbaiki memakai $request->alasan
            ]);

            // 3. 🔑 SOLUSI UTAMA BUG 1: Perbarui kolom 'requested_by' & 'alasan' di tabel penyusutan_aset
            $penyusutan->update([
                'status'       => 'menunggu',
                'requested_by' => Auth::id(), // Di-isi agar di blade tidak terbaca null!
                'alasan'       => $teksUsulan // Menyimpan flag teks tindakan peremajaan
            ]);

            DB::commit();

            $this->notifyKadeptLifecycleSubmission(
                'peremajaan',
                $penyusutan->asset->kode_aset ?? '-'
            );

            return redirect()->back()->with('success', 'Usulan peremajaan fisik aset berhasil dikirim ke Kepala Departemen.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses usulan: ' . $e->getMessage());
        }
    }

    public function ajukanPenghapusan(Request $request, $id_penyusutan)
    {
        if (in_array(Auth::user()->role, ['kadept', 'kadiv'])) {
            return redirect()->back()->with('error', 'Kepala departemen dan kepala divisi hanya dapat melakukan monitoring penyusutan.');
        }

        $request->validate([
            'alasan' => 'required|string|max:1000'
        ]);

        $penyusutan = PenyusutanAset::with('asset')->findOrFail($id_penyusutan);

        DB::beginTransaction();

        try {

            // ======================================================
            // CEK AGAR ASET TIDAK DIAJUKAN DUA KALI
            // ======================================================

            $cek = PenghapusanAset::where('id_aset', $penyusutan->id_aset)
                ->where('status', 'menunggu')
                ->exists();
            if ($cek) {
                DB::rollBack();

                return redirect()->back()->with(
                    'warning',
                    'Aset tersebut sedang dalam proses pengajuan penghapusan.'
                );
            }

            // ======================================================
            // SIMPAN DATA PENGHAPUSAN
            // ======================================================

            $penghapusan = PenghapusanAset::create([
                'id_aset'      => $penyusutan->id_aset,
                'alasan'       => $request->alasan,
                'status'       => 'menunggu',
                'requested_by' => Auth::id(),
            ]);

            // ======================================================
            // BUAT APPROVAL STAGE 1
            // ======================================================

            Approval::create([
                'approval_type' => PenghapusanAset::class,
                'reference_id'  => $penghapusan->id_penghapusan,
                'stage'         => 1,
                'status'        => 'menunggu',
                'requested_by'  => Auth::id(),
                'note'          => 'USULAN PENGHAPUSAN : '.$request->alasan
            ]);

            // ======================================================
            // UPDATE PENYUSUTAN
            // ======================================================

            $penyusutan->update([
                'requested_by' => Auth::id(),
                'status'       => 'menunggu',
                'alasan'       => 'USULAN PENGHAPUSAN: '.$request->alasan
            ]);

            DB::commit();

            $this->notifyKadeptLifecycleSubmission(
                'penghapusan',
                $penyusutan->asset->kode_aset ?? '-'
            );

            return redirect()->back()->with(
                'success',
                'Pengajuan penghapusan berhasil dikirim ke Kepala Departemen.'
            );
        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()->back()->with(
                'error',
                $e->getMessage()
            );
        }
    }

    public function cetakBaPeremajaanPdf($id)
    {
        // Ambil data log penyusutan/peremajaan berdasarkan ID
        $penyusutan = \App\Models\PenyusutanAset::with([
            'asset' => function($query) {
                $query->withTrashed()->with(['barang', 'lokasi', 'amil']);
            },
            'requestedBy'
        ])->findOrFail($id);

        // Ambil data approval polimorfik terkait rekor PenyusutanAset ini
        $approvals = \App\Models\Approval::where('reference_id', $penyusutan->id_penyusutan)
            ->where('approval_type', \App\Models\PenyusutanAset::class)
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

        $pdf = Pdf::loadView('superadmin.aset.penyusutan.berita-acara-pdf', [
            'penyusutan'      => $penyusutan,
            'kadeptSignature' => $kadeptSignature,
            'kadivSignature'  => $kadivSignature,
            'qrDataKadept'    => $qrDataKadept,
            'qrDataKadiv'     => $qrDataKadiv,
        ]);

        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('isRemoteEnabled', true);
        $pdf->setOption('isPhpEnabled', true);

        return $pdf->download('Berita_Acara_Peremajaan_' . ($penyusutan->asset->kode_aset ?? 'Aset') . '_' . now()->format('d-m-Y') . '.pdf');
    }

    public function printLaporanPenyusutan(Request $request)
{
    // 1. Logika Query & Filtering data Penyusutan / Peremajaan
    $query = \App\Models\PenyusutanAset::with([
        'asset' => function ($q) {
            $q->withTrashed()->with(['barang', 'jenis']);
        },
        'requestedBy'
    ]);

    if ($request->filled('tindakan')) {
        if ($request->tindakan == 'murni_susut') {
            $query->whereNull('requested_by');
        } elseif ($request->tindakan == 'peremajaan') {
            $query->whereNotNull('requested_by')->where('alasan', 'LIKE', '%peremajaan%');
        }
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Filter Berdasarkan Ruangan Lokasi
    if ($request->filled('id_lokasi')) {
        $query->whereHas('asset', function($q) use ($request) {
            $q->withTrashed()->where('id_lokasi', $request->id_lokasi);
        });
    }

    // 🔑 NEW FILTER LOGIC: Menyaring Berdasarkan Jenis Aset (Elektronik/Meubelair/dll)
    if ($request->filled('id_jenis')) {
        $query->whereHas('asset', function($q) use ($request) {
            $q->withTrashed()->where('id_jenis', $request->id_jenis);
        });
    }

    $dataPenyusutan = $query->orderBy('created_at', 'desc')->get();
    
    // Hitung total untuk manifes kriptografi
    $totalHargaPerolehan = $dataPenyusutan->sum('nilai_awal');
    $totalBebanPenyusutan = $dataPenyusutan->sum('nilai_penyusutan');

    // Metadata Audit Cetak
    $printedBy = $request->user()->name;
    $role      = $request->user()->role;
    $timestamp = now()->format('d M Y, H:i:s');

    // 2. Ambil Data User Pimpinan Aktif
    $userKadept = \App\Models\User::where('role', 'kadept')->first();
    $userKadiv  = \App\Models\User::where('role', 'kadiv')->first();

    $kepalaDept   = $userKadept->name ?? '........................................';
    $kepalaDivisi = $userKadiv->name ?? '........................................';

    // 3. 🔑 DIGITAL SIGNATURE HMACS-SHA256 BERBASIS DATA MANIFES AUDIT FINANSIAL
    $fingerprintKadept = $userKadept ? substr(md5($userKadept->id_user . 'kadept'), 0, 12) : 'KADEPT-EMPTY';
    $fingerprintKadiv  = $userKadiv ? substr(md5($userKadiv->id_user . 'kadiv'), 0, 12) : 'KADIV-EMPTY';

    $manifestKadept = "BAZNAS JABAR | Jenis: Laporan Penyusutan & Peremajaan | Oleh Kadept: {$kepalaDept} | Waktu: {$timestamp} | Nilai Awal: Rp " . number_format($totalHargaPerolehan, 0, ',', '.');
    $sigKadept = base64_encode(hash_hmac('sha256', $manifestKadept, 'SECRET_KEY_SYSTEM_BAZNAS_JABAR'));

    $manifestKadiv = "BAZNAS JABAR | Jenis: Laporan Penyusutan & Peremajaan | Oleh Kadiv: {$kepalaDivisi} | Waktu: {$timestamp} | Nilai Awal: Rp " . number_format($totalHargaPerolehan, 0, ',', '.');
    $sigKadiv = base64_encode(hash_hmac('sha256', $manifestKadiv, 'SECRET_KEY_SYSTEM_BAZNAS_JABAR'));

    $qrDataKadept = URL::signedRoute('reports.verify', [
        'document' => 'Laporan Penyusutan & Peremajaan',
        'role' => 'Kepala Departemen',
        'signer' => $kepalaDept,
        'printed_by' => $printedBy,
        'printed_at' => $timestamp,
        'fingerprint' => 'KEY-' . $fingerprintKadept,
        'digital_signature' => substr($sigKadept, 0, 25) . '...',
    ]);
    $qrDataKadiv  = URL::signedRoute('reports.verify', [
        'document' => 'Laporan Penyusutan & Peremajaan',
        'role' => 'Kepala Divisi',
        'signer' => $kepalaDivisi,
        'printed_by' => $printedBy,
        'printed_at' => $timestamp,
        'fingerprint' => 'KEY-' . $fingerprintKadiv,
        'digital_signature' => substr($sigKadiv, 0, 25) . '...',
    ]);

    // Log Tracker Audit Stamp bagian tengah
    $qrCodeSystem = "SIMASET AUDIT STAMP | Operator Cetak: {$printedBy} ({$role}) | Waktu: {$timestamp} | Status Data: Valid.";

    // 4. Kirim Notifikasi Pengawasan Ke Email/Sistem Pimpinan
    try {
        if ($userKadiv) {
            $userKadiv->notify(new \App\Notifications\AssetMutationNotification([
                'id_pindah' => null,
                'title'     => 'Log Pengawasan: Cetak Jurnal Penyusutan',
                'message'   => "User '{$printedBy}' ({$role}) baru saja mendownload Laporan Jurnal Penyusutan & Peremajaan.",
                'icon'      => 'fas fa-print',
                'bg_color'  => 'bg-info',
                'url'       => url('/laporan-aset')
            ]));
        }
        if ($userKadept) {
            $userKadept->notify(new \App\Notifications\AssetMutationNotification([
                'id_pindah' => null,
                'title'     => 'Log Pengawasan: Cetak Jurnal Penyusutan',
                'message'   => "User '{$printedBy}' ({$role}) baru saja mendownload Laporan Jurnal Penyusutan & Peremajaan.",
                'icon'      => 'fas fa-print',
                'bg_color'  => 'bg-info',
                'url'       => url('/laporan-aset')
            ]));
        }
    } catch (\Exception $eMail) { }

    return view('superadmin.reports.print_penyusutan', compact(
        'dataPenyusutan', 'qrDataKadept', 'qrDataKadiv', 'qrCodeSystem', 
        'printedBy', 'timestamp', 'kepalaDivisi', 'kepalaDept'
    ));
}

private function notifyKadeptLifecycleSubmission(string $jenisAksi, string $kodeAset): void
{
    try {
        $label = ucfirst($jenisAksi);
        $isPenghapusan = $jenisAksi === 'penghapusan';

        $details = [
            'id_pindah' => null,
            'title'     => "Pengajuan {$label} Aset Baru",
            'message'   => "Aset dengan kode {$kodeAset} memiliki usulan {$jenisAksi} dan memerlukan persetujuan Anda pada Stage 1.",
            'icon'      => $isPenghapusan ? 'fas fa-trash-alt' : 'fas fa-tools',
            'bg_color'  => $isPenghapusan ? 'bg-danger' : 'bg-info',
            'url'       => route('approvals.index'),
        ];

        User::where('role', 'kadept')->get()->each(function (User $kadept) use ($details) {
            $kadept->notify(new AssetMutationNotification($details));
        });
    } catch (\Exception $eMail) {
        // Pengajuan tetap sah meskipun kanal email/notifikasi sedang bermasalah.
    }
}
}
