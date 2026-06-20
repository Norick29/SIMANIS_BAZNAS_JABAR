<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Approval;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\PindahAset;
use App\Models\Signature;
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


class ApprovalController extends Controller
{
    /**
     * LIST APPROVAL
     */
    public function index()
    {
        $user = Auth::user();

        $query = Approval::with([
            'pindahAset.asset.barang',
            'pindahAset.asset.lokasi',
            'pindahAset.amilSebelum',
            'pindahAset.amilSesudah',
            'pindahAset.lokasiBaru',
            'requestedBy'
        ])
        ->where('status', 'menunggu');

        // FILTER BERDASARKAN ROLE
        if ($user->role === 'kadept') {

            $query->where('stage', 1);

        } elseif ($user->role === 'kadiv') {

            $query->where('stage', 2);

        }

        // SUPER ADMIN LIHAT SEMUA

        $approvals = $query
            ->latest()
            ->get();

        return view('superadmin.approval.index', compact('approvals'));
    }

    /**
     * DETAIL APPROVAL
     */
    public function show($id)
    {
        $approval = Approval::with([
            'pindahAset.asset.barang',
            'pindahAset.asset.lokasi',
            'pindahAset.amilSebelum',
            'pindahAset.amilSesudah',
            'pindahAset.lokasiBaru',
            'requestedBy',
            'pindahAset.approvals'
        ])->findOrFail($id);

        $user = Auth::user();

        // VALIDASI HAK APPROVAL
        if (
            ($approval->stage == 1 && $user->role != 'kadept') &&
            ($approval->stage == 2 && $user->role != 'kadiv') &&
            ($user->role != 'super admin')
        ) {
            return redirect()
                ->route('approvals.index')
                ->with('error', 'Anda tidak memiliki akses');
        }

        return view('superadmin.approval.show', compact('approval'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'password' => 'required'
        ]);

        if (!Hash::check($request->password, Auth::user()->password)) {

            return back()->with(
                'error',
                'Password salah'
            );
        }

        DB::beginTransaction();

        try {

            $approval = Approval::findOrFail($id);

            // UPDATE APPROVAL SEKARANG
            $approval->update([
                'status' => 'disetujui',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            $pindah = $approval->pindahAset;

            $digitalSignature = $this->generateDigitalSignature(
                $pindah
            );

            Signature::create([
                'id_aset' => $pindah->id_aset,
                'id_pindah' => $pindah->id_pindah,
                'user_id' => Auth::id(),

                'signed_data' =>
                    $digitalSignature['signed_data'],

                'signature' =>
                    $digitalSignature['signature'],

                'public_key_fingerprint' =>
                    $digitalSignature['fingerprint'],

                'algorithm' => 'RSA-SHA256',

                'signed_at' => now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | STAGE 1 → BUAT STAGE 2
            |--------------------------------------------------------------------------
            */

            if ($approval->stage == 1) {

                Approval::create([
                    'id_pindah' => $approval->id_pindah,
                    'stage' => 2,
                    'status' => 'menunggu',
                    'approved_by' => null,
                    'approved_at' => null,
                ]);

                // 🔔 [NOTIFIKASI 1]: Beritahu Pengaju/Staf bahwa Stage 1 di-ACC Kadept
                $pengaju = User::find($pindah->requested_by);
                if ($pengaju) {
                    $pengaju->notify(new AssetMutationNotification([
                        'id_pindah' => $pindah->id_pindah,
                        'title'     => 'Mutasi Disetujui Kadept (Stage 1)',
                        'message'   => "Pengajuan mutasi aset dengan kode {$pindah->asset->kode_aset} telah disetujui Kepala Departemen dan diteruskan ke Kepala Divisi.",
                        'icon'      => 'fas fa-check-circle',
                        'bg_color'  => 'bg-success',
                        'url'       => url('/approvals') 
                    ]));
                }

                // 🔔 [NOTIFIKASI 2]: Beritahu Semua Kadiv untuk Melakukan Approval Stage 2
                $kadivUsers = User::where('role', 'kadiv')->get();
                foreach ($kadivUsers as $kadiv) {
                    $kadiv->notify(new AssetMutationNotification([
                        'id_pindah' => $pindah->id_pindah,
                        'title'     => 'Permintaan Approval Stage 2',
                        'message'   => "Mutasi aset dengan kode {$pindah->asset->kode_aset} menunggu persetujuan Anda.",
                        'icon'      => 'fas fa-user-shield',
                        'bg_color'  => 'bg-primary',
                        'url'       => url('/approvals')
                    ]));
                }
            }

            /*
            |--------------------------------------------------------------------------
            | STAGE 2 → FINAL APPROVAL
            |--------------------------------------------------------------------------
            */

            if ($approval->stage == 2) {

                $pindah = $approval->pindahAset;
                $aset = $pindah->asset; // Ambil data aset terkait

                // 1. AMBIL DATA LOKASI BARU UNTUK GENERATE KODE
                $lokasiBaru = Lokasi::findOrFail($pindah->id_lokasi_baru);
                $kodeLokasiBaru = str_pad($lokasiBaru->kode_lokasi, 2, '0', STR_PAD_LEFT);

                // 2. PECAH KODE ASET LAMA UNTUK MENGAMBIL KOMPONEN YANG TIDAK BERUBAH
                // Struktur lama: {Lokasi}.{Kategori}.{Jenis}.{Barang}.{Sumber}.{Tahun}.{Urut}
                $partKode = explode('.', $aset->kode_aset);
                
                $kodeKategori = $partKode[1];
                $kodeJenis    = $partKode[2];
                $kodeBarang   = $partKode[3];
                $kodeSumber   = $partKode[4];
                $tahun        = $partKode[5];

                // 3. HITUNG NOMOR URUT BARU DI LOKASI TUJUAN
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

                // 4. SUSUN KODE ASET BARU
                $kodeAsetBaru = "{$kodeLokasiBaru}.{$kodeKategori}.{$kodeJenis}.{$kodeBarang}.{$kodeSumber}.{$tahun}.{$urutBaru}";

                /* ==========================================
                * 5. GENERATE QR CODE BARU (MENGGANTI YANG LAMA)
                * ========================================== */
                // Hapus file QR code lama dari storage jika filenya ada agar tidak memenuhi server
                if ($aset->qr_code && file_exists(public_path($aset->qr_code))) {
                    @unlink(public_path($aset->qr_code));
                }

                $qrDirectory = public_path('qr-assets');
                if (!file_exists($qrDirectory)) {
                    mkdir($qrDirectory, 0755, true);
                }

                $qrFileName = 'qr_' . Str::uuid() . '.png';
                $qrPathBaru = 'qr-assets/' . $qrFileName;
                
                // Generate Signed URL baru berbasis kode aset yang baru
                $signedUrl = URL::signedRoute('assets.scan', [
                    'kode_aset' => $kodeAsetBaru
                ]);

                $result = Builder::create()
                    ->writer(new PngWriter())
                    ->data($signedUrl)
                    ->size(300)
                    ->margin(5)
                    ->build();

                $result->saveToFile(public_path($qrPathBaru));

                /* ==========================================
                * 6. UPDATE DATA ASET SECARA KESELURUHAN
                * ========================================== */
                $aset->update([
                    'kode_aset' => $kodeAsetBaru,
                    'id_amil'   => $pindah->id_amil_sesudah,
                    'id_lokasi' => $pindah->id_lokasi_baru,
                    'qr_code'   => $qrPathBaru,
                ]);

                // UPDATE STATUS PINDAH
                $pindah->update([
                    'status' => 'disetujui'
                ]);

                // 🔔 [NOTIFIKASI 3]: Beritahu Pengaju/Staf bahwa Mutasi Selesai (Final Approved)
                $pengaju = User::find($pindah->requested_by);
                if ($pengaju) {
                    $pengaju->notify(new AssetMutationNotification([
                        'id_pindah' => $pindah->id_pindah,
                        'title'     => 'Mutasi Aset Disetujui Penuh (Final)',
                        'message'   => "Selamat, pengajuan mutasi aset untuk kode {$kodeAsetBaru} telah disetujui oleh Kepala Divisi. Kode aset dan QR Code baru telah diperbarui.",
                        'icon'      => 'fas fa-boxes',
                        'bg_color'  => 'bg-success',
                        'url'       => url('/riwayat-pindah-aset')
                    ]));
                }
            }

            DB::commit();

            return redirect()
                ->route('approvals.index')
                ->with(
                    'success',
                    'Approval berhasil diproses'
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with(
                'error',
                $e->getMessage()
            );
        }
    }

    public function riwayatPindah(Request $request)
    {
        $query = PindahAset::with([
            'asset.barang',
            'asset.lokasi',
            'amilSebelum',
            'amilSesudah',
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
        'lokasi_lama' => $pindah->asset?->lokasi?->nama_lokasi ?? 'N/A',
        'lokasi_baru' => $pindah->lokasiBaru?->nama_lokasi ?? 'N/A',
        'approved_by' => $user->name,
        'approved_at' => now()->toDateTimeString(),
    ]);

    // 2. Dekripsi Private Key
    try {
        $decryptedPrivateKey = Crypt::decryptString($user->private_key);
        
        // --- FIX MASALAH 1: Jika string ternyata ter-serialisasi, kita unserialize ---
        if (preg_match('/^s:\d+:"/', $decryptedPrivateKey) || @unserialize($decryptedPrivateKey) !== false) {
            // Jika strukturnya mentah seperti dump (ada teks s:1704), kita bersihkan manual dengan regex
            $decryptedPrivateKey = preg_replace('/^s:\d+:"|";$/', '', $decryptedPrivateKey);
        }
    } catch (\Exception $e) {
        throw new \Exception('Gagal melakukan dekripsi Private Key user.');
    }

    // --- FIX MASALAH 2: Rekonstruksi Format PEM OpenSSL (Baris Baru) ---
    // Bersihkan dulu dari spasi atau karakter aneh
    $cleanKey = trim($decryptedPrivateKey);
    
    // Ekstrak isi core key (ambil teks di antara BEGIN dan END)
    $searchBegin = "-----BEGIN PRIVATE KEY-----";
    $searchEnd   = "-----END PRIVATE KEY-----";
    
    // Jika formatnya menyatu tanpa baris baru, kita pisahkan core-nya
    $coreKey = str_replace([$searchBegin, $searchEnd, "\n", "\r", " "], "", $cleanKey);
    
    // Potong core key per 64 karakter sesuai standar RFC 7468 (PEM format)
    $chunkedCoreKey = chunk_split($coreKey, 64, "\n");
    
    // Gabungkan kembali menjadi format privat key yang disukai OpenSSL PHP
    $formattedPrivateKey = $searchBegin . "\n" . $chunkedCoreKey . $searchEnd;

    // 3. Load ke OpenSSL
    $privateKey = openssl_pkey_get_private($formattedPrivateKey);

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
        // 2. Cari data approval berdasarkan id_approval yang dilempar dari rute
        $approval = Approval::with('pindahAset')->findOrFail($id);

        // Jaring pengaman: jika statusnya sudah bukan 'pending', jangan izinkan diubah lagi
        if ($approval->status !== 'menunggu') {
            return redirect()->back()->with('error', 'Permintaan ini sudah diproses sebelumnya, bro.');
        }

        // 3. Verifikasi keamanan Password Pimpinan (Digital Signature Auth)
        $user = Auth::user();
        if (!Hash::check($request->password, $user->password)) {
            return redirect()->back()->with('error', 'Gagal memproses penolakan. Password konfirmasi Anda salah!');
        }

        // 4. Update data approval menjadi 'rejected'
        $approval->update([
            'status'      => 'ditolak',
            'approved_by' => $user->id_user,       // Mencatat siapa pimpinan yang menolak
            'approved_at' => now(),                // Waktu penolakan (digital timestamp)
            'note'  => $request->alasan,     // Menyimpan alasan kenapa ditolak (pastikan kolom ini ada di DB, atau sesuaikan namanya)
        ]);

        // 5. Update juga status di tabel induknya (pindah_asets) agar ikut sinkron menjadi 'rejected'
        if ($approval->pindahAset) {
            $approval->pindahAset->update([
                'status' => 'ditolak'
            ]);
        }

        /**
         * =========================================================================
         * 🔔 [NOTIFIKASI PENOLAKAN]: Beritahu Pengaju/Staf Bahwa Pengajuan Ditolak
         * =========================================================================
         */
        $pindah = $approval->pindahAset;
        $pengaju = User::find($pindah->requested_by);
        
        if ($pengaju) {
            // Tentukan jabatan penolak berdasarkan level stage
            $jabatanPenolak = ($approval->stage == 1) ? 'Kepala Departemen' : 'Kepala Divisi';

            $pengaju->notify(new AssetMutationNotification([
                'id_pindah' => $pindah->id_pindah,
                'title'     => "Mutasi Ditolak oleh {$jabatanPenolak}",
                'message'   => "Pengajuan mutasi aset dengan kode {$pindah->asset->kode_aset} ditolak. Alasan: \"{$request->alasan}\"",
                'icon'      => 'fas fa-times-circle',
                'bg_color'  => 'bg-danger', // Merah untuk penolakan
                'url'       => url('/riwayat-pindah-aset')
            ]));
        }

        // Sukses kembali ke halaman detail dengan alert hijau
        return redirect()->route('approvals.index')
            ->with('success', 'Permintaan perpindahan aset berhasil ditolak dan ditandatangani secara digital.');

    } catch (\Exception $e) {
        // Jika ada masalah sistem database/query, tangkap di sini biar gak crash halaman HTML-nya
        return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
    }
}

}