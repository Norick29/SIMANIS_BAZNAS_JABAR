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
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Amil</div>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Aset</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_aset }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Nilai Perolehan Aset</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($nilai_aset,0,',','.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Kerugian Aset (Rusak/Hilang)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($rugi_aset,0,',','.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line-down fa-2x text-gray-300"></i> {{-- Menggunakan ikon tren turun/kerugian --}}
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
                            <i class="fas fa-calculator fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Aset Baik</div>
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
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Aset Rusak</div>
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
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Aset Hilang</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $aset_hilang }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-search fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Maintenance Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_maintenance }} Pengajuan</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-spinner fa-2x text-gray-300"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_qrcode }}</div>
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
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">Digital Signature</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_signature }}</div>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Mutasi Bulan Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $mutasi_bulan_ini }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
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
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Approval Menunggu</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $antrean_approval }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-danger animate-spin"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

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