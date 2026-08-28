<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Approval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\PindahAset;
use App\Models\Signature;
use App\Models\PenyusutanAset;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Crypt;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use App\Models\Lokasi; // Import model Lokasi
use App\Models\Asset; // Import model Asset
use Carbon\Carbon;
use App\Models\User;
use App\Notifications\AssetMutationNotification;
use App\Services\RSAService;


class ApprovalController extends Controller
{
    /**
     * LIST APPROVAL
     */
    public function index()
{
    $user = Auth::user();
    $query = Approval::with([
        'requestedBy',

        // ==========================
        // MUTASI
        // ==========================
        'pindahAset.asset.barang',
        'pindahAset.asset.lokasi',
        'pindahAset.amilSebelum',
        'pindahAset.amilSesudah',
        'pindahAset.lokasiBaru',

        // ==========================
        // POLYMORPHIC
        // ==========================
        'reference',
        'reference.asset',
        'reference.asset.barang',
        'reference.asset.lokasi',
        'reference.asset.amil',
    ])
    ->where('status','menunggu');

    // ==========================
    // FILTER BERDASARKAN ROLE
    // ==========================

    if($user->role == 'kadept'){
        $query->where('stage',1);
    }
    elseif($user->role == 'kadiv'){
        $query->where('stage',2);
    }
    $approvals = $query
        ->latest()
        ->get();

    return view(
        'superadmin.approval.index',
        compact('approvals')
    );
}

    /**
     * DETAIL APPROVAL
     */
    public function show($id)
{
    $user = Auth::user();
    $approval = Approval::with([

        'requestedBy',

        // ==========================
        // MUTASI
        // ==========================

        'pindahAset.asset.barang',
        'pindahAset.asset.lokasi',
        'pindahAset.amilSebelum',
        'pindahAset.amilSesudah',
        'pindahAset.lokasiBaru',
        'pindahAset.approvals.approvedBy',

        // ==========================
        // POLYMORPHIC
        // ==========================

        'reference',
        'reference.asset',
        'reference.asset.barang',
        'reference.asset.lokasi',
        'reference.asset.amil',
        'reference.requestedBy'

    ])->findOrFail($id);

    // ===================================
    // VALIDASI ROLE
    // ===================================

    if(($approval->stage == 1 && $user->role != 'kadept') && ($approval->stage == 2 && $user->role != 'kadiv') && ($user->role != 'super admin')){

        return redirect()
            ->route('approvals.index')
            ->with(
                'error',
                'Anda tidak memiliki akses.'
            );
    }

    return view(
        'superadmin.approval.show',
        compact('approval')
    );
}

    public function approve(Request $request, $id)
{
    $request->validate([
        'password' => 'required'
    ]);

    if (!Hash::check($request->password, Auth::user()->password)) {
        return back()->with('error', 'Password salah');
    }

    // Siapkan penampung data untuk kebutuhan kirim email di luar transaction
    $dataNotif = [
        'stage' => null,
        'requested_by' => null,
        'id_pindah' => null,
        'kode_aset_lama' => null,
        'kode_aset_baru' => null,
        'jenis_aksi'       => null,
    ];

    DB::beginTransaction();

    try {
        // 1. Ambil data approval beserta relasinya secara fresh
        $approval = Approval::with('pindahAset.asset')->findOrFail($id);
        $stageSebelumnya = $approval->stage;

        // Jaring pengaman agar tidak di-approve dua kali
        if ($approval->status !== 'menunggu') {
            DB::rollBack();
            return back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        // 2. UPDATE STATUS APPROVAL INTI
        $approval->update([
            'status' => 'disetujui',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

    if ($approval->approval_type === null || $approval->approval_type === \App\Models\PindahAset::class) {
        $pindah = $approval->pindahAset;
        if (!$pindah) {
            throw new \Exception('Data perpindahan aset tidak ditemukan.');
        }

        // Simpan data mentah awal untuk backup notifikasi email nanti
        $dataNotif['stage'] = $stageSebelumnya;
        $dataNotif['requested_by'] = $pindah->requested_by;
        $dataNotif['id_pindah'] = $pindah->id_pindah;
        $dataNotif['kode_aset_lama'] = $pindah->asset->kode_aset ?? '-';
        $dataNotif['jenis_aksi'] = 'pindah';

        // 3. GENERATE DIGITAL SIGNATURE
        $digitalSignature = $this->generateDigitalSignature($pindah);

        Signature::create([
            'id_aset' => $pindah->id_aset,
            'id_pindah' => $pindah->id_pindah,
            'user_id' => Auth::id(),
            'signed_data' => $digitalSignature['signed_data'],
            'signature' => $digitalSignature['signature'],
            'public_key_fingerprint' => $digitalSignature['fingerprint'],
            'algorithm' => 'RSA-SHA256',
            'signed_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | ALUR STAGE 1 → BUAT PERMINTAAN STAGE 2
        |--------------------------------------------------------------------------
        */
        if ($stageSebelumnya == 1) {
            Approval::create([
                'id_pindah' => $pindah->id_pindah,
                'stage' => 2,
                'status' => 'menunggu',
                'requested_by' => $pindah->requested_by,
                'approved_by' => null,
                'approved_at' => null,
            ]);
            
            // Opsional: Jika status induk ingin diubah jadi 'proses' saat stage 1 acc
            $pindah->update([
                'status' => 'menunggu' // Tetap menunggu karena butuh approval kadiv
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | ALUR STAGE 2 → FINAL APPROVAL (UBAH KODE & LOKASI ASET)
        |--------------------------------------------------------------------------
        */
        if ($stageSebelumnya == 2) {
            $aset = $pindah->asset; 
            if (!$aset) {
                throw new \Exception('Data aset tidak ditemukan.');
            }

            // A. Ambil Data Lokasi Baru
            $lokasiBaru = Lokasi::findOrFail($pindah->id_lokasi_baru);
            $kodeLokasiBaru = str_pad($lokasiBaru->kode_lokasi, 2, '0', STR_PAD_LEFT);

            // B. Pecah Kode Aset Lama
            $partKode = explode('.', $aset->kode_aset);
            $kodeKategori = $partKode[1] ?? '00';
            $kodeJenis    = $partKode[2] ?? '00';
            $kodeBarang   = $partKode[3] ?? '00';
            $kodeSumber   = $partKode[4] ?? '00';
            $tahun        = $partKode[5] ?? date('Y');

            // C. Hitung Nomor Urut Baru
            $lastAsetDiLokasiBaru = Asset::where('id_lokasi', $pindah->id_lokasi_baru)
                ->where('id_kategori', $aset->id_kategori)
                ->orderBy('id_aset', 'desc')
                ->first();

            if ($lastAsetDiLokasiBaru) {
                $lastUrut = (int) substr($lastAsetDiLokasiBaru->kode_aset, -3);
                $nextUrut = $lastUrut + 1;
            } else {
                $nextUrut = 1;
            }
            $urutBaru = str_pad($nextUrut, 3, '0', STR_PAD_LEFT);

            // D. Susun Kode Aset Baru
            $kodeAsetBaru = "{$kodeLokasiBaru}.{$kodeKategori}.{$kodeJenis}.{$kodeBarang}.{$kodeSumber}.{$tahun}.{$urutBaru}";
            $dataNotif['kode_aset_baru'] = $kodeAsetBaru;

            // E. Regenerate QR Code
            if ($aset->qr_code && file_exists(public_path($aset->qr_code))) {
                @unlink(public_path($aset->qr_code));
            }

            $qrDirectory = public_path('qr-assets');
            if (!file_exists($qrDirectory)) {
                mkdir($qrDirectory, 0755, true);
            }

            $qrFileName = 'qr_' . Str::uuid() . '.png';
            $qrPathBaru = 'qr-assets/' . $qrFileName;
            
            $signedUrl = URL::signedRoute('assets.scan', ['kode_aset' => $kodeAsetBaru]);

            $result = Builder::create()
                ->writer(new PngWriter())
                ->data($signedUrl)
                ->size(300)
                ->margin(5)
                ->build();

            $result->saveToFile(public_path($qrPathBaru));

            // F. UPDATE DATABASE UTAMA SECARA EKSPLISIT 🔑
            $aset->update([
                'kode_aset' => $kodeAsetBaru,
                'id_amil'   => $pindah->id_amil_sesudah,
                'id_lokasi' => $pindah->id_lokasi_baru,
                'qr_code'   => $qrPathBaru,
            ]);

            $pindah->update([
                'status' => 'disetujui'
            ]);
        }
    // =========================================================================
        // 🔀 PERCABANGAN JALUR 2: LIFECYCLE AUDIT (PEREMAJAAN / PENGHAPUSAN)
        // =========================================================================
        } elseif ($approval->approval_type === \App\Models\PenyusutanAset::class || $approval->approval_type === \App\Models\PenghapusanAset::class) {
            
            $penyusutan = null;

            if ($approval->approval_type === \App\Models\PenyusutanAset::class) {
                // Jika tipenya penyusutan (Peremajaan)
                $penyusutan = \App\Models\PenyusutanAset::with('asset.barang')->findOrFail($approval->reference_id);
            } else {
                // Jika tipenya penghapusan
                $penyusutan = \App\Models\PenghapusanAset::with('asset.barang')->findOrFail($approval->reference_id);
            }
            
            // ⚡ PERBAIKAN UTAMA: Identifikasi aksi langsung dari properti note milik data approval yang sedang diproses saat ini!
            $noteLower = strtolower($approval->note ?? '');
            $isPeremajaan = \Illuminate\Support\Str::contains($noteLower, 'peremajaan');
            $jenisAksi = $isPeremajaan ? 'peremajaan' : 'penghapusan';

            $dataNotif['stage'] = $stageSebelumnya;
            $dataNotif['requested_by'] = $penyusutan->requested_by;
            $dataNotif['kode_aset_lama'] = $penyusutan->asset->kode_aset ?? '-';
            $dataNotif['jenis_aksi'] = $jenisAksi;

            if ($stageSebelumnya == 1) {
                // ⚡ PERBAIKAN CRITICAL: Salin dan teruskan nilai 'note' dari Stage 1 ke rekor Stage 2 milik Kadiv
                Approval::create([
                    'approval_type' => $approval->approval_type,
                    'reference_id'  => $approval->reference_id,
                    'stage'         => 2,
                    'status'        => 'menunggu',
                    'requested_by'  => $penyusutan->requested_by,
                    'note'          => $approval->note // 🔑 Kunci identitas aksi ada di sini, bro!
                ]);
            }

            if ($stageSebelumnya == 2) {
                $aset = $penyusutan->asset;
                if (!$aset) {
                    throw new \Exception('Data aset tidak ditemukan.');
                }

                if ($isPeremajaan) {
                    // 🔄 EKSEKUSI FINAL PEREMAJAAN
                    $aset->update([
                        'kondisi' => 'baik'
                    ]);
                    $penyusutan->update([
                        'status' => 'disetujui'
                    ]);
                }else {

                // ==========================================================
                // REVISI
                // FINAL APPROVAL PENGHAPUSAN
                // ==========================================================

                // Simpan histori penghapusan
                $penyusutan->update([
                        'alasan' => str_replace('USULAN PENGHAPUSAN: ', '', $approval->note),
                        'status' => 'disetujui'
                    ]);

                \App\Models\PenyusutanAset::where('id_aset', $aset->id_aset)
                    ->where('status', 'menunggu')
                    ->update([
                        'status' => 'disetujui'
                    ]);
                // Update status aset
                $aset->update([
                    'status' => 'nonaktif'
                ]);
                // Arsipkan aset
                $aset->delete();
            }

                // Perbarui status induk log penyusutan menjadi selesai
                $penyusutan->update([
                    'status' => 'disetujui'
                ]);
            }
        }

        // COMMIT SEKARANG: mysql akan memaksa data kesimpan permanen
        DB::commit();

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal memproses approval database: ' . $e->getMessage());
    }

    // 🔑 PROSES KIRIM EMAIL DI LUAR LINGKUP TRANSACTION
    try {
        $pengaju = User::find($dataNotif['requested_by']);

        if ($dataNotif['stage'] == 1) {
            if ($pengaju) {
                $titleText = $dataNotif['jenis_aksi'] == 'pindah' ? 'Mutasi Disetujui Kadept (Stage 1)' : 'Usulan '.ucfirst($dataNotif['jenis_aksi']).' Disetujui Kadept';
                $msgText = $dataNotif['jenis_aksi'] == 'pindah' 
                    ? "Pengajuan mutasi aset dengan kode {$dataNotif['kode_aset_lama']} telah disetujui Kepala Departemen dan diteruskan ke Kepala Divisi."
                    : "Usulan {$dataNotif['jenis_aksi']} aset dengan kode {$dataNotif['kode_aset_lama']} disetujui Kepala Departemen & diteruskan ke Kepala Divisi.";
                $urlStageOne = match ($dataNotif['jenis_aksi']) {
                    'pindah' => route('riwayat.pindah'),
                    'peremajaan' => route('penyusutan.index'),
                    default => route('penghapusan.index'),
                };

                $pengaju->notify(new AssetMutationNotification([
                    'id_pindah' => $dataNotif['id_pindah'],
                    'title'     => $titleText,
                    'message'   => $msgText,
                    'icon'      => 'fas fa-check-circle',
                    'bg_color'  => 'bg-success',
                    'url'       => $urlStageOne
                ])); 
            }

            $kadivUsers = User::where('role', 'kadiv')->get(); 
            foreach ($kadivUsers as $kadiv) {
                $msgKadiv = $dataNotif['jenis_aksi'] == 'pindah'
                    ? "Mutasi aset dengan kode {$dataNotif['kode_aset_lama']} menunggu persetujuan Anda."
                    : "Usulan {$dataNotif['jenis_aksi']} aset dengan kode {$dataNotif['kode_aset_lama']} membutuhkan validasi Anda."; 

                $kadiv->notify(new AssetMutationNotification([
                    'id_pindah' => $dataNotif['id_pindah'],
                    'title'     => 'Permintaan Approval Stage 2',
                    'message'   => $msgKadiv,
                    'icon'      => 'fas fa-user-shield',
                    'bg_color'  => 'bg-primary',
                    'url'       => route('approvals.index')
                ])); 
            }
        }

        if ($dataNotif['stage'] == 2) {
            if ($pengaju) {
                $kodeFinal = $dataNotif['kode_aset_baru'] ?? $dataNotif['kode_aset_lama']; 
                
                if($dataNotif['jenis_aksi'] == 'pindah') {
                    $titleFinal = 'Mutasi Aset Disetujui Penuh (Final)';
                    $msgFinal = "Selamat, pengajuan mutasi aset untuk kode {$kodeFinal} telah disetujui oleh Kepala Divisi. Kode aset dan QR Code baru telah diperbarui."; 
                    $urlFinal = route('riwayat.pindah'); 
                } elseif($dataNotif['jenis_aksi'] == 'peremajaan') {
                    $titleFinal = 'Peremajaan Aset Disetujui (Final)';
                    $msgFinal = "Selamat, usulan peremajaan aset dengan kode {$kodeFinal} disetujui Kepala Divisi. Umur ekonomis aset berhasil diperpanjang +5 tahun.";
                    $urlFinal = route('penyusutan.index');
                } else {
                    $titleFinal = 'Penghapusan Aset Disetujui (Final)';
                    $msgFinal = "Selamat, pengajuan pemusnahan aset dengan kode {$kodeFinal} disetujui Kepala Divisi. Data telah diarsipkan ke riwayat log penghapusan.";
                    $urlFinal = route('penghapusan.index');
                }

                $pengaju->notify(new AssetMutationNotification([
                    'id_pindah' => $dataNotif['id_pindah'],
                    'title'     => $titleFinal,
                    'message'   => $msgFinal,
                    'icon'      => 'fas fa-boxes',
                    'bg_color'  => 'bg-success',
                    'url'       => $urlFinal
                ])); 
            }
        }
    } catch (\Exception $eMail) { 
        // Abaikan limitasi mailtrap [cite: 2382]
    }

    return redirect()
        ->route('approvals.index')
        ->with('success', 'Approval berhasil diproses dan disimpan ke database.');
}

    public function riwayatPindah(Request $request)
    {
        $query = PindahAset::with([
            'asset.barang',
            'asset.lokasi',
            'amilSebelum',
            'amilSesudah',
            'lokasiLama',
            'lokasiBaru',
            'requestedBy'
        ]);

        // 1. FILTER PERIODE BULAN & TAHUN (DEFAULT: BULAN & TAHUN BERJALAN)
        $selectedMonth = $request->input('bulan', now()->month);
        $selectedYear = $request->input('tahun', now()->year);

        $query->whereMonth('created_at', $selectedMonth)
            ->whereYear('created_at', $selectedYear);

        // 2. FILTER STATUS PERPINDAHAN
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 3. FILTER LOKASI TUJUAN (LOKASI BARU)
        if ($request->filled('id_lokasi_baru')) {
            $query->where('id_lokasi_baru', $request->id_lokasi_baru);
        }

        // 4. 🔍 FITUR SEARCH NAVBAR GLOBAL (Logical Grouping)
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('status', 'like', '%' . $search . '%')
                        ->orWhere('alasan', 'like', '%' . $search . '%');

                $subQuery->orWhereHas('asset', function ($qAsset) use ($search) {
                    $qAsset->where('kode_aset', 'like', '%' . $search . '%')
                        ->orWhereHas('barang', function ($qBarang) use ($search) {
                            $qBarang->where('nama_barang', 'like', '%' . $search . '%');
                        });
                });

                $subQuery->orWhereHas('amilSebelum', function ($q) use ($search) {
                    $q->where('nama', 'like', '%' . $search . '%');
                })->orWhereHas('amilSesudah', function ($q) use ($search) {
                    $q->where('nama', 'like', '%' . $search . '%');
                });

                $subQuery->orWhereHas('requestedBy', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        $riwayat = $query->latest()
                        ->paginate(10)
                        ->appends($request->all());

        // MASTER DATA UNTUK DROPDOWN FILTER
        $monthsList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $minYear = PindahAset::min('created_at') ? Carbon::parse(PindahAset::min('created_at'))->year : now()->year;
        $yearsList = range($minYear, now()->year + 1);

        // Ambil data semua lokasi untuk dropdown filter
        $lokasis = Lokasi::orderBy('nama_lokasi')->get();

        return view(
            'superadmin.aset.riwayat_pindah.index',
            compact('riwayat', 'selectedMonth', 'selectedYear', 'monthsList', 'yearsList', 'lokasis')
        );
    }

    public function showBeritaAcara($id)
    {
        $pindah = PindahAset::with([
            'asset.barang',
            'asset.lokasi',
            'amilSebelum',
            'amilSesudah',
            'lokasiBaru',
            'lokasiLama',
            'approvals.approvedBy',
            'requestedBy'
        ])->findOrFail($id);

        return view(
            'superadmin.aset.riwayat_pindah.berita-acara',
            compact('pindah')
        );
    }

    public function downloadBeritaAcaraPdf($id)
{
    $pindah = PindahAset::with([
        'asset.barang',
        'asset.lokasi',
        'amilSebelum',
        'amilSesudah',
        'lokasiBaru',
        'lokasiLama',
        'approvals.approvedBy'
    ])->findOrFail($id);

    $approvals = $pindah->approvals()
        ->with('approvedBy')
        ->where('status', 'disetujui')
        ->get();

    $kadeptSignature = $approvals->where('stage', 1)->first();
    $kadivSignature = $approvals->where('stage', 2)->first();

    // Data QR Code dipersiapkan dalam bentuk array/string teks biasa
    $qrDataKadept = null;
    if ($kadeptSignature) {
        $qrDataKadept = URL::signedRoute('approvals.verify', ['id' => $kadeptSignature->id_approval]);
    }

    $qrDataKadiv = null;
    if ($kadivSignature) {
        $qrDataKadiv = URL::signedRoute('approvals.verify', ['id' => $kadivSignature->id_approval]);
    }

    $pdf = Pdf::loadView('superadmin.aset.riwayat_pindah.berita-acara-pdf', [
        'pindah'          => $pindah,
        'kadeptSignature' => $kadeptSignature,
        'kadivSignature'  => $kadivSignature,
        'qrDataKadept'    => $qrDataKadept,
        'qrDataKadiv'     => $qrDataKadiv,
    ]);

    $pdf->setPaper('A4', 'portrait');
    
    // Opsi wajib agar DomPDF diijinkan membaca file lokal & mengeksekusi PHP inline (termasuk QR Code)
    $pdf->setOption('isRemoteEnabled', true);
    $pdf->setOption('isPhpEnabled', true);

    return $pdf->download('Berita_Acara_' . $pindah->asset->kode_aset . '_' . now()->format('d-m-Y') . '.pdf');
}

public function verifyPublicApproval($id)
{
    // Ambil data approval berdasarkan ID yang di-scan
    // Gunakan relasi 'approvedBy' dan tambahkan relasi ke transaksi/aset jika perlu
    $approval = \App\Models\Approval::with(['approvedBy'])->findOrFail($id);
    
    // Cari data pindah aset pendukungnya (opsional, disesuaikan dengan DB kamu)
    // $pindah = PindahAset::where('id_pindah', $approval->id_pindah)->first();

    // Oper data ke halaman view publik
    return view('superadmin.aset.riwayat_pindah.verify-public', compact('approval'));
}


private function generateDigitalSignature($pindah)
{
    $user = Auth::user();

    // 1. Buat data JSON transaksi
    $data = json_encode([
        'id_pindah'   => $pindah->id_pindah ?? null,
        'kode_aset'   => $pindah->asset?->kode_aset ?? 'N/A',
        'lokasi_lama' => $pindah->lokasiLama?->nama_lokasi ?? 'N/A',
        'lokasi_baru' => $pindah->lokasiBaru?->nama_lokasi ?? 'N/A',
        'approved_by' => $user->name,
        'approved_at' => now()->toDateTimeString(),
    ]);

    if (empty($user->private_key) || empty($user->public_key)) {
        $this->regenerateUserKeyPair($user);
    }

    // 2. Dekripsi Private Key
    try {
        $decryptedPrivateKey = $this->decryptUserPrivateKey($user->private_key);
    } catch (\Exception $e) {
        $this->regenerateUserKeyPair($user);
        $decryptedPrivateKey = $this->decryptUserPrivateKey($user->private_key);
    }

    // --- FIX MASALAH 2: Rekonstruksi Format PEM OpenSSL (Baris Baru) ---
    // Bersihkan dulu dari spasi atau karakter aneh
    $cleanKey = trim($decryptedPrivateKey);

    $privateKey = openssl_pkey_get_private($cleanKey);
    if (!$privateKey) {
        $this->clearOpenSslErrors();
    }
    
    // Ekstrak isi core key (ambil teks di antara BEGIN dan END)
    $searchBegin = str_contains($cleanKey, 'BEGIN RSA PRIVATE KEY')
        ? "-----BEGIN RSA PRIVATE KEY-----"
        : "-----BEGIN PRIVATE KEY-----";
    $searchEnd = str_contains($cleanKey, 'END RSA PRIVATE KEY')
        ? "-----END RSA PRIVATE KEY-----"
        : "-----END PRIVATE KEY-----";
    
    if (!$privateKey) {
        // Jika formatnya menyatu tanpa baris baru, kita pisahkan core-nya
        $coreKey = str_replace([$searchBegin, $searchEnd, "\n", "\r", " "], "", $cleanKey);

        // Potong core key per 64 karakter sesuai standar RFC 7468 (PEM format)
        $chunkedCoreKey = chunk_split($coreKey, 64, "\n");

        // Gabungkan kembali menjadi format privat key yang disukai OpenSSL PHP
        $formattedPrivateKey = $searchBegin . "\n" . $chunkedCoreKey . $searchEnd . "\n";

        // 3. Load ke OpenSSL
        $privateKey = openssl_pkey_get_private($formattedPrivateKey);
    }

    if (!$privateKey) {
        // Jika masih gagal, kita muntahkan error internal OpenSSL agar jelas di layar
        $openSslError = '';
        while ($msg = openssl_error_string()) {
            $openSslError .= $msg . ' | ';
        }
        throw new \Exception('Private key masih tidak valid setelah diformat. OplenSSL Error: ' . $openSslError);
    }

    // 4. Proses Tanda Tangan Digital (Signing)
    $isSigned = openssl_sign(
        $data,
        $rawSignature,
        $privateKey,
        OPENSSL_ALGO_SHA256
    );

    if (!$isSigned) {
        throw new \Exception('Gagal membuat digital signature.');
    }

    if (PHP_VERSION_ID < 80000) {
        openssl_free_key($privateKey);
    }

    $base64Signature = base64_encode($rawSignature);

    return [
        'signed_data' => $data,
        'signature'   => $base64Signature,
        'fingerprint' => sha1($user->public_key),
    ];
}

private function decryptUserPrivateKey($encryptedPrivateKey)
{
    try {
        $decryptedPrivateKey = Crypt::decrypt($encryptedPrivateKey);
    } catch (\Exception $e) {
        $decryptedPrivateKey = Crypt::decryptString($encryptedPrivateKey);
    }

    if (is_string($decryptedPrivateKey)) {
        $unserialized = @unserialize($decryptedPrivateKey);
        if ($unserialized !== false || $decryptedPrivateKey === 'b:0;') {
            $decryptedPrivateKey = $unserialized;
        }
    }

    if (!is_string($decryptedPrivateKey) || trim($decryptedPrivateKey) === '') {
        throw new \Exception('Private Key user kosong atau tidak valid.');
    }

    return $decryptedPrivateKey;
}

private function regenerateUserKeyPair(User $user)
{
    $keyPair = app(RSAService::class)->generateKeyPair();

    $user->forceFill([
        'public_key' => $keyPair['public_key'],
        'private_key' => $keyPair['private_key'],
    ])->save();

    $user->refresh();
}

private function clearOpenSslErrors()
{
    while (openssl_error_string()) {
        //
    }
}

public function reject(Request $request, $id)
{
    // 1. Validasi input dari form reject blade
    $request->validate([
        'alasan'   => 'required|string|max:1000',
        'password' => 'required',
    ], [
        'alasan.required'   => 'Alasan penolakan wajib diisi, bro.',
        'password.required' => 'Password konfirmasi wajib diisi untuk tanda tangan digital.',
    ]);

    try {
        $approval = Approval::findOrFail($id);

        // Jaring pengaman: jika statusnya sudah bukan 'pending', jangan izinkan diubah lagi
        if ($approval->status !== 'menunggu') {
            return redirect()->back()->with('error', 'Permintaan ini sudah diproses sebelumnya, bro.');
        }

        // Verifikasi keamanan Password Pimpinan (Digital Signature Auth)
        $user = Auth::user();
        if (!Hash::check($request->password, $user->password)) {
            return redirect()->back()->with('error', 'Gagal memproses penolakan. Password konfirmasi Anda salah!');
        }

        $originalNote = $approval->note ?? '';
        $isPeremajaan = \Illuminate\Support\Str::contains(strtolower($originalNote), 'peremajaan');
        $isPenghapusan = $approval->approval_type === \App\Models\PenghapusanAset::class
            || \Illuminate\Support\Str::contains(strtolower($originalNote), 'penghapusan');

        // 🔑 1. DEFINISIKAN VARIABEL DEFAULT DI SINI AGAR BEBAS UNDEFINED
        $kodeAsetInfo = '-';
        $requestedBy = null;
        $msgNotif = "Pengajuan ditolak oleh pimpinan.";
        $urlNotif = url('/dashboard');

        // Update data approval menjadi 'ditolak'
        $approval->update([
            'status'      => 'ditolak',
            'approved_by' => $user->id_user,
            'approved_at' => now(),
            'note'        => $request->alasan,
        ]);

        if ($approval->approval_type === null || $approval->approval_type === \App\Models\PindahAset::class) {
            
            // --- JALUR INDUK PINDAH ASET ---
            if ($approval->pindahAset) {
                $approval->pindahAset->update([
                    'status' => 'ditolak'
                ]);
            }
            $pindah = $approval->pindahAset;
            if ($pindah) {
                $requestedBy = $pindah->requested_by;
                $kodeAsetInfo = $pindah->asset->kode_aset ?? '-';
            }
            $msgNotif = "Pengajuan mutasi aset dengan kode {$kodeAsetInfo} ditolak. Alasan: \"{$request->alasan}\"";
            $urlNotif = route('riwayat.pindah'); 

        } elseif ($approval->approval_type === \App\Models\PenyusutanAset::class) {
            
            // --- JALUR INDUK AUDIT LIFECYCLE PENYUSUTAN ---
            if ($isPeremajaan) {
                // Jalur A: Jika yang ditolak adalah Peremajaan
                $penyusutan = \App\Models\PenyusutanAset::with('asset')->find($approval->reference_id);
                if ($penyusutan) {
                    $penyusutan->update([
                        'status' => 'ditolak',
                        'alasan' => 'DITOLAK PIMPINAN PEREMAJAAN: ' . $request->alasan
                    ]);
                    $requestedBy = $penyusutan->requested_by;
                    $kodeAsetInfo = $penyusutan->asset->kode_aset ?? '-';
                }
                $msgNotif = "Pengajuan PEREMAJAAN untuk aset dengan kode {$kodeAsetInfo} ditolak pimpinan. Alasan: \"{$request->alasan}\"";
            }

            $urlNotif = route('penyusutan.index');
        } elseif ($approval->approval_type === \App\Models\PenghapusanAset::class || $isPenghapusan) {
            $penghapusan = \App\Models\PenghapusanAset::with('asset')->find($approval->reference_id);
            if ($penghapusan) {
                $penghapusan->update([
                    'status' => 'ditolak',
                    'alasan' => 'DITOLAK PIMPINAN PENGHAPUSAN: ' . $request->alasan
                ]);
                
                \App\Models\PenyusutanAset::where('id_aset', $penghapusan->id_aset)
                    ->where('status', 'menunggu')
                    ->update([
                        'status' => 'ditolak',
                        'alasan' => 'DITOLAK PIMPINAN PENGHAPUSAN: ' . $request->alasan
                    ]);

                $requestedBy = $penghapusan->requested_by;
                $kodeAsetInfo = $penghapusan->asset->kode_aset ?? '-';
            }
            $msgNotif = "Pengajuan PENGHAPUSAN untuk aset dengan kode {$kodeAsetInfo} ditolak pimpinan. Alasan: \"{$request->alasan}\"";
            $urlNotif = route('penghapusan.index');
        }

        /**
         * =========================================================================
         * 🔔 [NOTIFIKASI PENOLAKAN]: Beritahu Pengaju/Staf Bahwa Pengajuan Ditolak
         * =========================================================================
         */
        if ($requestedBy) {
            $pengaju = User::find($requestedBy);
            if ($pengaju) {
                $jabatanPenolak = ($approval->stage == 1) ? 'Kepala Departemen' : 'Kepala Divisi';
                $pengaju->notify(new AssetMutationNotification([
                    'id_pindah' => $approval->id_pindah, 
                    'title'     => "Permintaan Ditolak oleh {$jabatanPenolak}",
                    'message'   => $msgNotif,
                    'icon'      => 'fas fa-times-circle',
                    'bg_color'  => 'bg-danger',
                    'url'       => $urlNotif
                ]));
            }
        }

        // Sukses kembali ke halaman list approval
        return redirect()->route('approvals.index')
            ->with('success', 'Permintaan kebijakan aset berhasil ditolak dan ditandatangani secara digital.');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
    }
}

}
