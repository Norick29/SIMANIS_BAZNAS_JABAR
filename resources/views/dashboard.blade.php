@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
        </h1>
    </div>

    {{-- ========================================================================= --}}
    {{-- BARISAN KOTAK STATISTIK (CARDS WIDGET) --}}
    {{-- ========================================================================= --}}
    <div class="row">
        {{-- INFO JIKA AKUN ADALAH SUPER ADMIN --}}
        @if(auth()->user()->role == 'super admin')
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total User</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $count_user }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Penanggung Jawab (Amil)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $count_amil }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Volume Aset</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_aset }} Unit</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KARTU 1: KAPITALISASI HARGA AWAL --}}
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Nilai Perolehan Awal</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($nilai_aset,0,',','.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-history fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🔑 KARTU BARU 2: NILAI BUKU AKURAT BERJALAN PASCA PENYUSUTAN & PEREMAJAAN --}}
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Nilai Buku Aset Saat Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-primary">Rp {{ number_format($nilai_buku_berjalan ?? 0,0,',','.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 🔑 KARTU BARU 3: ANGGARAN YANG BERHASIL DI-RECOVERY LEWAT OPTIMALISASI PEREMAJAAN --}}
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Nilai Pemulihan Peremajaan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($total_pemulihan_peremajaan ?? 0,0,',','.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sync-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KARTU 4: TOTAL KERUGIAN BERDASARKAN PENGHAPUSAN PERMANEN & HILANG --}}
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Penghapusan & Kehilangan</div>
                            <div class="h5 mb-0 font-weight-bold text-danger">Rp {{ number_format($rugi_aset,0,',','.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300" style="transform: scaleY(-1);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Biaya Perawatan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($total_biaya_maintenance,0,',','.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Kondisi Aset Baik</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $aset_baik }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Kondisi Aset Rusak</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $aset_rusak }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Kondisi Aset Hilang</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $aset_hilang }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-search-minus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Proses Perbaikan Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_maintenance }} Tindakan</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-spin fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Aset Ber QR Code</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_qrcode }} Label</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-qrcode fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">E-Signature Terverifikasi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_signature }} Dokumen</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-signature fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Mutasi Penempatan (Bulan Ini)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $mutasi_bulan_ini }} Ringkasan</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- INTERUPSI ANTREAN APPROVAL UNTUK PIMPINAN --}}
        @if(auth()->user()->role == 'kadept' || auth()->user()->role == 'kadiv')
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2 bg-gradient-light animate-pulse">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Approval Menunggu Tindakan</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $antrean_approval }} Pengajuan</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-exclamation-circle fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- ========================================================================= --}}
    {{-- ⚡ SEKTOR BARU: RINGKASAN AUDIT FINANSIAL & KEKRITISAN ASET --}}
    {{-- ========================================================================= --}}
    <div class="row mb-4">
        {{-- KOTAK KIRI: ESTIMASI PENYUSUTAN TAHUNAN BERJALAN --}}
        <div class="col-lg-6 mb-4 mb-lg-0">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-light">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-calculator mr-1"></i> Estimasi Alokasi Beban Penyusutan Periode Ini
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Berikut adalah ringkasan total beban penyusutan murni tahunan yang dibukukan oleh sistem otomatisasi berdasarkan master kebijakan.</p>
                    <div class="row align-items-center">
                        <div class="col-6 border-right">
                            <span class="text-xs text-uppercase font-weight-bold text-muted d-block">Total Beban Akumulasi</span>
                            <span class="h4 font-weight-bold text-danger mb-0">
                                Rp {{ number_format($total_beban_penyusutan ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="col-6 pl-4">
                            <span class="text-xs text-uppercase font-weight-bold text-muted d-block">Rata-rata Penyusutan / Aset</span>
                            <span class="h5 font-weight-bold text-dark mb-0">
                                Rp {{ number_format($rata_rata_penyusutan ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="progress progress-sm mb-2" style="height: 8px;">
                        <div class="progress-bar bg-danger shadow-sm" role="progressbar" 
                             style="width: {{ $total_aset > 0 ? (($aset_rusak + $aset_hilang) / $total_aset) * 100 : 0 }}%;" 
                             aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small class="text-muted font-italic">Indikator rasio depresiasi fisik terhadap total volume inventaris aktif.</small>
                </div>
            </div>
        </div>

        {{-- KOTAK KANAN: ANTRIAN AUDIT & TINDAKAN KEKRITISAN --}}
        <div class="col-lg-6">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-light">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-gavel mr-1"></i> Status Logistik & Antrean Kebijakan Manajemen
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush small">
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div>
                                <h6 class="mb-0 font-weight-bold text-gray-800">Aset Melebihi Umur Ekonomis</h6>
                                <small class="text-muted">Inventaris tua yang berstatus kritis & wajib diaudit fisik.</small>
                            </div>
                            <span class="badge badge-danger badge-pill font-weight-bold px-3 py-2 shadow-sm">{{ $aset_kritis_count ?? 0 }} Aset</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div>
                                <h6 class="mb-0 font-weight-bold text-gray-800">Usulan Peremajaan Aktif</h6>
                                <small class="text-muted">Data yang sedang diajukan staff untuk pemulihan nilai buku.</small>
                            </div>
                            <span class="badge badge-info badge-pill font-weight-bold px-3 py-2 shadow-sm">{{ $peremajaan_pending_count ?? 0 }} Usulan</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div>
                                <h6 class="mb-0 font-weight-bold text-gray-800">Usulan Penghapusan Aktif</h6>
                                <small class="text-muted">Aset rusak total yang menunggu persetujuan pemusnahan dokumen.</small>
                            </div>
                            <span class="badge badge-dark badge-pill font-weight-bold px-3 py-2 shadow-sm">{{ $penghapusan_pending_count ?? 0 }} Usulan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- ========================================================================= --}}
    {{-- SEKTOR GRAFIK / CHART (SAMA BESAR & SEJAJAR) --}}
    {{-- ========================================================================= --}}
    <div class="row">

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-light">
                    {{-- Judul grafik diubah ke text-success --}}
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-chart-pie mr-1"></i>Distribusi Kondisi Aset
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 320px; width: 100%;">
                        <canvas id="pieAsset"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-2 bg-light d-flex flex-row align-items-center justify-content-between">
                    {{-- Judul grafik diubah ke text-success --}}
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-chart-bar mr-1"></i>Mutasi Aset Tahun {{ $tahun_pilihan }}
                    </h6>
                    
                    {{-- Form Dropdown Filter Tahun --}}
                    <form action="{{ url()->current() }}" method="GET" id="formFilterTahun" class="m-0">
                        {{-- Dropdown diubah ke text-success --}}
                        <select name="tahun_grafik" class="form-control form-control-sm font-weight-bold text-success" onchange="document.getElementById('formFilterTahun').submit();" style="cursor: pointer;">
                            @for($year = now()->year; $year >= 2024; $year--)
                                <option value="{{ $year }}" {{ $tahun_pilihan == $year ? 'selected' : '' }}>
                                    Tahun {{ $year }}
                                </option>
                            @endfor
                        </select>
                    </form>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height: 320px; width: 100%;">
                        <canvas id="chartMutasi"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ========================================================================= --}}
    {{-- SEKTOR TABEL RIWAYAT AKTIVITAS --}}
    {{-- ========================================================================= --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-light">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-history mr-1"></i>Aktivitas Perpindahan Aset Terbaru
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0" style="font-size: 14px;">
                            <thead class="thead-light">
                                <tr class="text-center">
                                    <th>Kode Aset</th>
                                    <th>Nama Barang</th>
                                    <th>Lokasi Lama</th>
                                    <th>Lokasi Baru</th>
                                    <th>Status Dokumen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_mutasi as $mutasi)
                                    <tr>
                                        {{-- Teks Kode Aset diubah ke text-success --}}
                                        <td class="text-center font-weight-bold text-success">{{ $mutasi->asset->kode_aset ?? '-' }}</td>
                                        <td>{{ $mutasi->asset->barang->nama_barang ?? '-' }}</td>
                                        <td>{{ $mutasi->asset->lokasi->nama_lokasi ?? '-' }}</td>
                                        <td>{{ $mutasi->lokasiBaru->nama_lokasi ?? '-' }}</td>
                                        <td class="text-center">
                                            @if($mutasi->status == 'disetujui' || $mutasi->status == 'approved')
                                                <span class="badge badge-success px-3 py-1">Disetujui</span>
                                            @elseif($mutasi->status == 'menunggu' || $mutasi->status == 'pending')
                                                <span class="badge badge-warning px-3 py-1">Pending</span>
                                            @else
                                                <span class="badge badge-danger px-3 py-1">{{ ucfirst($mutasi->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-folder-open mr-1"></i>Belum ada data aktivitas perpindahan aset.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- CSS KUSTOM UNTUK ANIMASI PENGINGAT PIMPINAN --}}
<style>
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.01); box-shadow: 0 0 12px rgba(231, 74, 59, 0.25); }
    100% { transform: scale(1); }
}
.animate-pulse {
    animation: pulse 2s infinite ease-in-out;
}
</style>

{{-- SCRIPT GRAPH ENGINE --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// --- CONFIG PIE CHART ---
new Chart(document.getElementById('pieAsset'), {
    type: 'pie',
    data: {
        labels: ['Baik', 'Rusak', 'Hilang'],
        datasets: [{
            data: @json($chartKondisi),
            backgroundColor: ['#28a745', '#dc3545', '#6c757d'],
            borderWidth: 2
        }]
    },
    options: {
        maintainAspectRatio: false,
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// --- CONFIG BAR CHART ---
new Chart(document.getElementById('chartMutasi'), {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        datasets: [{
            label: 'Jumlah Mutasi Aset Tahun ' + '{{ $tahun_pilihan }}',
            data: @json($chartMutasi),
            backgroundColor: '#28a745', // 🔑 SUDAH DIUBAH: Menggunakan warna hijau Bootstrap (#28a745)
            borderRadius: 4
        }]
    },
    options: {
        maintainAspectRatio: false,
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});
</script>
@endsection