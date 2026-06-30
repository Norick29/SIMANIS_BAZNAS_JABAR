<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Amil;
use App\Models\Asset;
use App\Models\Approval;
use App\Models\PindahAset;
use App\Models\Maintenance;
use App\Models\Signature;
use App\Models\PenyusutanAset;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
   public function index(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();

        /*
        |--------------------------------------------------------------------------
        | Default Values
        |--------------------------------------------------------------------------
        */
        $count_user = 0;
        $count_amil = 0;
        $total_aset = 0;
        $aset_baik = 0;
        $aset_rusak = 0;
        $aset_hilang = 0;
        $nilai_aset = 0;
        $rugi_aset = 0;
        $total_qrcode = 0;
        $total_signature = 0;
        $total_maintenance = 0;
        $mutasi_bulan_ini = 0;
        $antrean_approval = 0;
        $approval_disetujui = 0;
        $approval_ditolak = 0;
        $total_biaya_maintenance = 0;
        $biaya_maintenance_aktif = 0;
        
        // 🔑 INISIALISASI VARIABEL FINANSIAL BARU
        $nilai_buku_berjalan = 0;
        $total_pemulihan_peremajaan = 0;

        $recent_mutasi = collect();
        $antrean_list = collect();

        /*
        |--------------------------------------------------------------------------
        | Global Statistics
        |--------------------------------------------------------------------------
        */
        $count_user = User::count();
        $count_amil = Amil::count();
        $total_aset = Asset::count();
        $aset_baik = Asset::where('kondisi', 'baik')->count();
        $aset_rusak = Asset::where('kondisi', 'rusak')->count();
        $aset_hilang = Asset::where('kondisi', 'hilang')->count();
        
        // 1. Nilai Perolehan Awal: total_harga disimpan stabil sebagai nilai perolehan asli.
        $nilai_aset = Asset::sum('total_harga');

        $assetsAktif = Asset::with('penyusutanRecords')
            ->where('status', 'aktif')
            ->get();

        // 2. 🔑 HITUNG NILAI BUKU BERJALAN AKTIF (Setelah Susut saat ini di database)
        $nilai_buku_berjalan = $assetsAktif->sum(function ($asset) {
            return $asset->nilai_buku;
        });

        // 3. 🔑 HITUNG TOTAL ANGGARAN RECOVERY HASIL PEREMAJAAN ASET (50% dari Nilai Penyusutan)
        $total_pemulihan_peremajaan = PenyusutanAset::where('status', 'disetujui')
            ->where('alasan', 'LIKE', '%peremajaan%')
            ->get()
            ->sum(function($item) {
                return $item->nilai_penyusutan * 0.50;
            });

        // 4. 🔑 HITUNG KERUGIAN RIIL (Nilai Buku Aset yang Dihapus Permanen + Total Harga Aset Hilang)
        $nilaiAsetDihapus = Asset::onlyTrashed()
            ->with('penyusutanRecords')
            ->get()
            ->sum(function ($asset) {
                return $asset->nilai_buku;
            });

        $nilaiAsetHilang = Asset::with('penyusutanRecords')
            ->where('status', 'aktif')
            ->where(DB::raw('LOWER(kondisi)'), 'hilang')
            ->get()
            ->sum(function ($asset) {
                return $asset->nilai_buku;
            });
        
        $rugi_aset = $nilaiAsetDihapus + $nilaiAsetHilang;

        // Informasi QR, E-Signature, dan Maintenance
        $total_qrcode = Asset::whereNotNull('qr_code')->count();
        $total_signature = Signature::count();
        $total_maintenance = Maintenance::where('status', 'proses')->count();
        $total_biaya_maintenance = Maintenance::sum('biaya'); 
        $biaya_maintenance_aktif = Maintenance::where('status', 'proses')->sum('biaya');

        $mutasi_bulan_ini = PindahAset::whereMonth('created_at', now()->month)->count();
        $tahun_pilihan = $request->get('tahun_grafik', now()->year);

        /*
        |--------------------------------------------------------------------------
        | Pie Chart Kondisi Aset
        |--------------------------------------------------------------------------
        */
        $chartKondisi = [
            Asset::where('kondisi', 'baik')->count(),
            Asset::where('kondisi', 'rusak')->count(),
            Asset::where('kondisi', 'hilang')->count(),
        ];

        /*
        |--------------------------------------------------------------------------
        | Bar Chart Mutasi Bulanan
        |--------------------------------------------------------------------------
        */
        $chartMutasi = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartMutasi[] = PindahAset::whereYear('created_at', $tahun_pilihan)
                ->whereMonth('created_at', $i)
                ->count();
        }

        /*
        |--------------------------------------------------------------------------
        | Recent Mutasi
        |--------------------------------------------------------------------------
        */
        $recent_mutasi = PindahAset::with([
            'asset.barang',
            'asset.lokasi',
            'lokasiBaru'
        ])
            ->latest()
            ->take(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Role-Based Interruption (KADEPT)
        |--------------------------------------------------------------------------
        */
        if ($user->role == 'kadept') {
            $antrean_approval = Approval::where('status', 'menunggu')->where('stage', 1)->count();
            $approval_disetujui = Approval::where('status', 'disetujui')->where('stage', 1)->count();
            $approval_ditolak = Approval::where('status', 'ditolak')->where('stage', 1)->count();
            $antrean_list = Approval::with([
                'pindahAset.asset.barang',
                'pindahAset.lokasiBaru',
                'requestedBy'
            ])
                ->where('status', 'menunggu')
                ->where('stage', 1)
                ->latest()
                ->take(5)
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Role-Based Interruption (KADIV)
        |--------------------------------------------------------------------------
        */
        if ($user->role == 'kadiv') {
            $antrean_approval = Approval::where('status', 'menunggu')->where('stage', 2)->count();
            $approval_disetujui = Approval::where('status', 'disetujui')->where('stage', 2)->count();
            $approval_ditolak = Approval::where('status', 'ditolak')->where('stage', 2)->count();
            $antrean_list = Approval::with([
                'pindahAset.asset.barang',
                'pindahAset.lokasiBaru',
                'requestedBy'
            ])
                ->where('status', 'menunggu')
                ->where('stage', 2)
                ->latest()
                ->take(5)
                ->get();
        }

        // 1. Hitung total akumulasi beban penyusutan yang sudah terjadi dari engine otomatis
        $total_beban_penyusutan = PenyusutanAset::whereNull('requested_by')
            ->orderBy('created_at')
            ->orderBy('id_penyusutan')
            ->get()
            ->groupBy('id_aset')
            ->sum(function ($records) {
                return $records->last()->nilai_penyusutan ?? 0;
            });

        // 2. Hitung rata-rata nilai penyusutan per item aset
        $total_rekor_susut = PenyusutanAset::whereNull('requested_by')->distinct('id_aset')->count('id_aset');
        $rata_rata_penyusutan = $total_rekor_susut > 0 ? ($total_beban_penyusutan / $total_rekor_susut) : 0;

        // 3. Hitung jumlah aset yang umurnya sudah melebihi umur ekonomis (Aset Kritis)
        $tahunSekarang = (int)date('Y');
        $aset_kritis_count = Asset::where('status', 'aktif')
            ->whereHas('jenis.masterPenyusutan', function($query) use ($tahunSekarang) {
                $query->whereRaw('? - assets.tahun >= master_penyusutan.umur_ekonomis', [$tahunSekarang]);
            })->count();

        // 4. Hitung antrean usulan tindakan yang sedang menggantung di approvals
        $peremajaan_pending_count = PenyusutanAset::where('status', 'menunggu')
            ->where('alasan', 'LIKE', '%peremajaan%')
            ->count();

        $penghapusan_pending_count = PenyusutanAset::where('status', 'menunggu')
            ->where('alasan', 'NOT LIKE', '%peremajaan%')
            ->whereNotNull('requested_by')
            ->count();
        
        return view('dashboard', compact(
            'count_user', 'count_amil', 'total_aset', 'aset_baik', 'aset_rusak',
            'aset_hilang', 'nilai_aset', 'rugi_aset', 'total_qrcode', 'total_signature',
            'total_maintenance', 'mutasi_bulan_ini', 'antrean_approval', 'approval_disetujui',
            'approval_ditolak', 'recent_mutasi', 'antrean_list', 'chartKondisi', 'chartMutasi',
            'tahun_pilihan', 'total_biaya_maintenance', 'biaya_maintenance_aktif',
            'nilai_buku_berjalan', 'total_pemulihan_peremajaan',
            'total_beban_penyusutan', 'rata_rata_penyusutan', 
            'aset_kritis_count', 'peremajaan_pending_count', 'penghapusan_pending_count' // 🔑 Kirim ke Blade baru!
        ));
    }
}
