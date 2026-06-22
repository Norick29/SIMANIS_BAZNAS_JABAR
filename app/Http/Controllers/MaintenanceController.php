<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Maintenance;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class MaintenanceController extends Controller
{
public function index(Request $request)
{
    $query = Maintenance::with([
        'asset.barang'
    ]);

    // =========================================================================
    // 🔍 FITUR SEARCH NAVBAR (SINKRON DENGAN NAVBAR GLOBAL - AMAN DARI FILTER)
    // =========================================================================
    if ($request->filled('q')) {
        $search = $request->q;
        $query->where(function ($subQuery) use ($search) {
            // 1. Search langsung di tabel maintenance itu sendiri
            $subQuery->where('jenis', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%')
                    ->orWhere('deskripsi', 'like', '%' . $search . '%');

            // 2. Search merujuk ke relasi di tabel assets (melalui asset)
            $subQuery->orWhereHas('asset', function ($qAsset) use ($search) {
                $qAsset->where('kode_aset', 'like', '%' . $search . '%')
                    
                    // Turun lagi ke relasi nama_barang di dalam asset
                    ->orWhereHas('barang', function ($qBarang) use ($search) {
                        $qBarang->where('nama_barang', 'like', '%' . $search . '%');
                    });
            });
        });
    }

    // 1. FILTER PERIODE BULAN & TAHUN (DEFAULT: BULAN & TAHUN SEKARANG)
    $selectedMonth = $request->input('bulan', now()->month);
    $selectedYear = $request->input('tahun', now()->year);

    // Kunci query berdasarkan bulan dan tahun pada kolom tanggal_mulai
    $query->whereMonth('tanggal_mulai', $selectedMonth)
          ->whereYear('tanggal_mulai', $selectedYear);

    // 2. FILTER JENIS MAINTENANCE
    if ($request->filled('jenis')) {
        $query->where('jenis', $request->jenis);
    }

    // 3. FILTER STATUS
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $maintenances = $query
        ->orderBy('tanggal_mulai', 'desc')
        ->paginate(10)
        ->appends($request->all());

    $assetsReadyToMaintain = Asset::with('barang')
        ->where('kondisi', 'rusak')
        ->get();

    // OPSI LIST BULAN UNTUK FILTER BLADE
    $monthsList = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];

    // OPSI LIST TAHUN
    $minYear = Maintenance::min('tanggal_mulai') ? \Carbon\Carbon::parse(Maintenance::min('tanggal_mulai'))->year : now()->year;
    $yearsList = range($minYear, now()->year + 1);

    return view(
        'superadmin.aset.maintenance.index',
        compact(
            'maintenances',
            'assetsReadyToMaintain',
            'selectedMonth',
            'selectedYear',
            'monthsList',
            'yearsList'
        )
    );
}

    public function store(Request $request)
    {
        $request->validate([
            'id_aset'       => 'required|exists:assets,id_aset',
            'jenis'         => 'required|in:perbaikan,servis,kalibrasi',
            'biaya'         => 'required|numeric|min:0',
            'tanggal_mulai' => 'required|date',
            'deskripsi'     => 'required|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            // 1. Simpan log data maintenance baru (Default status: pending)
            Maintenance::create([
                'id_aset'       => $request->id_aset,
                'jenis'         => $request->jenis,
                'biaya'         => $request->biaya,
                'status'        => 'diproses', 
                'tanggal_mulai' => $request->tanggal_mulai,
                'deskripsi'     => $request->deskripsi,
            ]);

            // 2. OTOMATISASI: Kunci status aset menjadi 'nonaktif' agar tidak bisa dimutasi/dipindahkan selama diservis
            $aset = Asset::findOrFail($request->id_aset);
            $aset->update([
                'status' => 'nonaktif'
            ]);

            DB::commit();
            return redirect()->route('maintenances.index')->with('success', 'Aset berhasil masuk antrean maintenance dan dinonaktifkan sementara.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses data maintenance: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
{
    $maintenance = Maintenance::findOrFail($id);

    // proteksi tambahan di sisi server
    if ($maintenance->status == 'selesai') {
        return redirect()->route('maintenances.index')
            ->with('error', 'Gagal! Data maintenance yang telah selesai tidak dapat diubah kembali.');
    }

    $request->validate([
        'tingkat_kerusakan' => 'required|in:ringan,sedang,berat',
        'biaya'             => 'required|numeric|min:0',
        'deskripsi'         => 'required|string|max:1000'
    ]);

    DB::beginTransaction();

    try {
        $maintenance = Maintenance::findOrFail($id);
        
        // 1. Hitung Target Estimasi berdasarkan Input Tingkat Kerusakan
        $tanggalMulai = \Carbon\Carbon::parse($maintenance->tanggal_mulai);
        $hariEstimasi = $request->tingkat_kerusakan == 'ringan' ? 1 : ($request->tingkat_kerusakan == 'sedang' ? 3 : 7);
        $targetEstimasi = (clone $tanggalMulai)->addDays($hariEstimasi);

        $tanggalSelesaiNyata = now();

        // FIX: Bulatkan angka desimalnya agar tidak merusak database VARCHAR
        $durasiNyataHari = (int) ceil($tanggalMulai->diffInDays($tanggalSelesaiNyata));
        $textDurasiNyata = $durasiNyataHari == 0 ? '1 Hari Kerja' : $durasiNyataHari . ' Hari Kerja';

        $stringLogDurasi = "Realisasi: " . $textDurasiNyata . " (Target " . strtoupper($request->tingkat_kerusakan) . ")";

        // 5. Update Log Perawatan
        $maintenance->update([
            'status'          => 'selesai', // Auto status selesai
            'biaya'           => $request->biaya, // Sesuai kuitansi nyata
            'tanggal_selesai' => $tanggalSelesaiNyata, // Auto-fill waktu server
            'durasi'          => $stringLogDurasi, // Auto-kalkulasi gabungan
            'deskripsi'       => "Tindakan Teknisi: " . $request->deskripsi,
        ]);

        // 6. OTOMATISASI FISIK: Kembalikan kondisi aset menjadi Baik dan Aktif kembali
        $aset = Asset::findOrFail($maintenance->id_aset);
        $aset->update([
            'kondisi' => 'baik',
            'status'  => 'aktif'
        ]);

        DB::commit();
        return redirect()->route('maintenances.index')->with('success', 'Aset telah selesai diperbaiki dan otomatis diaktifkan kembali ke sistem.');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Gagal memproses penutupan maintenance: ' . $e->getMessage());
    }
}

    /* =========================================================================
     * 3. DESTROY METHOD (Batalkan Transaksi / Hapus Riwayat)
     * ========================================================================= */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $maintenance = Maintenance::findOrFail($id);
            $aset = Asset::findOrFail($maintenance->id_aset);

            // Jika log maintenance dihapus padahal statusnya belum selesai, kembalikan status aset ke kondisi semula
            if ($maintenance->status != 'selesai') {
                $aset->update([
                    'kondisi' => 'rusak',
                    'status'  => 'aktif' // Aktif di sini artinya bisa diakses tombol mutasi/deletenya lagi
                ]);
            }

            // Eksekusi soft delete log maintenance
            $maintenance->delete();

            DB::commit();
            return redirect()->route('maintenances.index')->with('success', 'Catatan maintenance berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function reportMaintenancePrint(Request $request)
{
    // Jalankan query dengan eager loading relasi ke asset dan barang di dalamnya
    $query = Maintenance::with(['asset.barang', 'asset.lokasi']);

    // --- LOGIKA FILTERING ---
    if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
        $query->whereBetween('tanggal_mulai', [$request->tanggal_awal, $request->tanggal_akhir]);
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Ambil data hasil filter
    $maintenances = $query->orderBy('tanggal_mulai', 'desc')->get();
    $totalBiaya = $maintenances->sum('biaya');

    // Menyusun string informasi parameter filter untuk judul dokumen
    $filterInfo = [];
    if ($request->filled('tanggal_awal') && $request->filled('tanggal_akhir')) {
        $filterInfo[] = 'Periode: ' . date('d/m/Y', strtotime($request->tanggal_awal)) . ' s.d ' . date('d/m/Y', strtotime($request->tanggal_akhir));
    }
    if ($request->filled('status')) {
        $filterInfo[] = 'Status: ' . strtoupper($request->status);
    }
    
    $stringFilter = count($filterInfo) > 0 ? implode(' | ', $filterInfo) : 'Semua Riwayat Maintenance';

    // Metadata Audit Cetak
    $printedBy = $request->user()->name;
    $role      = $request->user()->role;
    $timestamp = now()->format('d M Y, H:i:s');

    // Ambil data user pimpinan aktif
    $userKadept = User::where('role', 'kadept')->first();
    $userKadiv  = User::where('role', 'kadiv')->first();

    $kepalaDept   = $userKadept->name ?? '........................................';
    $kepalaDivisi = $userKadiv->name ?? '........................................';

    // --- 🔑 DIGITAL SIGNATURE DOUBLE TTD INSTAN (MAINTENANCE) ---
    $fingerprintKadept = $userKadept ? substr(md5($userKadept->id_user . 'kadept_maint'), 0, 12) : 'KADEPT-EMPTY';
    $fingerprintKadiv  = $userKadiv ? substr(md5($userKadiv->id_user . 'kadiv_maint'), 0, 12) : 'KADIV-EMPTY';

    $manifestKadept = "BAZNAS JABAR | Jenis: Laporan Maintenance | Oleh Kadept: {$kepalaDept} | Waktu: {$timestamp} | Total Biaya: Rp " . number_format($totalBiaya, 0, ',', '.');
    $sigKadept = base64_encode(hash_hmac('sha256', $manifestKadept, 'SECRET_KEY_SYSTEM_BAZNAS_JABAR'));

    $manifestKadiv = "BAZNAS JABAR | Jenis: Laporan Maintenance | Oleh Kadiv: {$kepalaDivisi} | Waktu: {$timestamp} | Total Biaya: Rp " . number_format($totalBiaya, 0, ',', '.');
    $sigKadiv = base64_encode(hash_hmac('sha256', $manifestKadiv, 'SECRET_KEY_SYSTEM_BAZNAS_JABAR'));

    $qrDataKadept = "VERIFIED_KADEPT\nDoc: Laporan Perawatan Aset\nKey Fingerprint: KEY-{$fingerprintKadept}\nSignature: " . substr($sigKadept, 0, 25) . "...";
    $qrDataKadiv  = "VERIFIED_KADIV\nDoc: Laporan Perawatan Aset\nKey Fingerprint: KEY-{$fingerprintKadiv}\nSignature: " . substr($sigKadiv, 0, 25) . "...";

    // Log Tracker Audit Tengah
    $qrCodeSystem = "SIMASET BAZNAS JABAR | Jenis: Laporan Riwayat Maintenance | Total Biaya: Rp " . number_format($totalBiaya, 0, ',', '.') . " | Generator: {$printedBy} ({$role}) | Waktu Cetak: {$timestamp} | Status Data: Sah Terverifikasi";

    // Kirim Notifikasi Pengawasan Ke Email Pimpinan (Kadiv)
    try {
        if ($userKadiv) {
            $userKadiv->notify(new \App\Notifications\AssetMutationNotification([
                'id_pindah' => null,
                'title'     => 'Log Pengawasan: Cetak Laporan Maintenance',
                'message'   => "User '{$printedBy}' ({$role}) baru saja mendownload Laporan Rekap Perawatan/Maintenance dengan total pengeluaran biaya Rp " . number_format($totalBiaya, 0, ',', '.'),
                'icon'      => 'fas fa-print',
                'bg_color'  => 'bg-info',
                'url'       => url('/laporan-maintenance')
            ]));
        }
        sleep(2);

    if ($userKadept) {
        $userKadept->notify(new \App\Notifications\AssetMutationNotification([
            'id_pindah' => null,
            'title'     => 'Log Pengawasan: Cetak Laporan Maintenance',
            'message'   => "User '{$printedBy}' ({$role}) baru saja mendownload Laporan Rekap Perawatan/Maintenance dengan total pengeluaran biaya Rp " . number_format($totalBiaya, 0, ',', '.'),
            'icon'      => 'fas fa-print',
            'bg_color'  => 'bg-info',
            'url'       => url('/laporan-maintenance')
        ]));
    }
    } catch (\Exception $eMail) { }

    return view('superadmin.reports.print_maintenance', compact(
        'maintenances', 'totalBiaya', 'stringFilter', 'qrDataKadept', 'qrDataKadiv',
        'qrCodeSystem', 'printedBy', 'timestamp', 'kepalaDivisi', 'kepalaDept'
    ));
}
}
