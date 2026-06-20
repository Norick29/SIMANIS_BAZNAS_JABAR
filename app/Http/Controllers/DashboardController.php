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

        // 🔑 VARIABEL BARU UNTUK RINCIAN OPERASIONAL FINANSIAL
        $total_biaya_maintenance = 0;
        $biaya_maintenance_aktif = 0;

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
        $nilai_aset = Asset::sum('total_harga');

        $rugi_aset = Asset::whereIn('kondisi', [
            'rusak',
            'hilang'
        ])->sum('total_harga');

        $total_qrcode = Asset::whereNotNull('qr_code')->count();
        $total_signature = Signature::count();

        $total_maintenance = Maintenance::where('status', 'proses')->count();

        // 🔑 HITUNG RINCIAN BIAYA MAINTENANCE BERDASARKAN PROPERTI 'biaya' PADA MODEL
        $total_biaya_maintenance = Maintenance::sum('biaya'); 
        $biaya_maintenance_aktif = Maintenance::where('status', 'proses')->sum('biaya');

        $mutasi_bulan_ini = PindahAset::whereMonth(
            'created_at',
            now()->month
        )->count();

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
        | KADEPT
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
        | KADIV
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

        return view('dashboard', compact(
            'count_user', 'count_amil', 'total_aset', 'aset_baik', 'aset_rusak',
            'aset_hilang', 'nilai_aset', 'rugi_aset', 'total_qrcode', 'total_signature',
            'total_maintenance', 'mutasi_bulan_ini', 'antrean_approval', 'approval_disetujui',
            'approval_ditolak', 'recent_mutasi', 'antrean_list', 'chartKondisi', 'chartMutasi',
            'tahun_pilihan',
            'total_biaya_maintenance', 'biaya_maintenance_aktif' // 🔑 Kirim ke depan, bro!
        ));
    }
}
