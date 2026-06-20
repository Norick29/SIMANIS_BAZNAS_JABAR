@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tools"></i> Manajemen Maintenance Aset
        </h1>
    </div> 

    {{-- VALIDATION & SESSION ALERTS --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    @foreach (['success', 'error', 'warning'] as $type)
        @if (session($type))
            <div class="alert alert-{{ $type == 'error' ? 'danger' : $type }} alert-dismissible fade show">
                {{ session($type) }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif
    @endforeach

    {{-- DATA TABLE CARD --}}
    <div class="card shadow mb-4">
        
        {{-- HEADER --}}
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2">
                {{-- Tombol Tambah Jadwal Tergantung Hak Akses Kamu (Bisa di-uncomment jika dipakai) --}}
                {{-- <a href="#" class="btn btn-success btn-sm mr-2" data-toggle="modal" data-target="#createMaintenanceModal">
                    <i class="fas fa-plus"></i> Tambah Perawatan
                </a> --}}

                {{-- TOMBOL TRIGGER FILTER --}}
                <button class="btn btn-secondary btn-sm" type="button" data-toggle="collapse" data-target="#filterMaintenance">
                    <i class="fas fa-filter"></i> Filter Periode
                </button>
            </div>

            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-list"></i> Daftar Maintenance - Periode {{ $monthsList[$selectedMonth] }} {{ $selectedYear }}
            </h6>
        </div>

        {{-- CONTAINER TOGGLE COLLAPSE FILTER (Dipindah ke luar card-body agar tabel presisi) --}}
        <div class="collapse {{ request()->hasAny(['bulan', 'tahun', 'jenis', 'status']) ? 'show' : '' }}" id="filterMaintenance">
            <div class="card-body bg-light border-bottom">
                <form method="GET" action="{{ route('maintenances.index') }}">
                    <div class="row">
                        {{-- FILTER BULAN --}}
                        <div class="col-md-3">
                            <label class="font-weight-bold">Bulan</label>
                            <select name="bulan" class="form-control auto-filter">
                                @foreach($monthsList as $num => $name)
                                    <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- FILTER TAHUN --}}
                        <div class="col-md-3">
                            <label class="font-weight-bold">Tahun</label>
                            <select name="tahun" class="form-control auto-filter">
                                @foreach($yearsList as $yr)
                                    <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>
                                        {{ $yr }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- FILTER JENIS MAINTENANCE --}}
                        <div class="col-md-3">
                            <label class="font-weight-bold">Jenis</label>
                            <select name="jenis" class="form-control auto-filter">
                                <option value="">Semua Jenis</option>
                                <option value="rutin" {{ request('jenis') == 'rutin' ? 'selected' : '' }}>Rutin</option>
                                <option value="perbaikan" {{ request('jenis') == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                            </select>
                        </div>

                        {{-- FILTER STATUS --}}
                        <div class="col-md-3">
                            <label class="font-weight-bold">Status</label>
                            <select name="status" class="form-control auto-filter">
                                <option value="">Semua Status</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="proses" {{ request('status') == 'proses' ? 'selected' : '' }}>Proses</option>
                                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>
                    </div>

                    {{-- TOMBOL AKSI FORM --}}
                    <div class="mt-3 text-right">
                        <a href="{{ route('maintenances.index') }}" class="btn btn-secondary">Reset</a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-search"></i> Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- BODY UTAMA (Hanya ada satu card-body bersih sekarang) --}}
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>No</th>
                            <th>Kode Aset</th>
                            <th>Nama Barang</th>
                            <th>Jenis Perawatan</th>
                            <th>Biaya</th>
                            <th>Status</th>
                            <th>Mulai</th>
                            <th>Selesai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($maintenances as $m)
                            <tr class="text-center">
                                <td>{{ $maintenances->firstItem() + $loop->index }}</td>
                                <td class="font-weight-bold text-success">
                                    {{ $m->asset->kode_aset ?? '-' }}
                                </td>
                                <td class="text-left">
                                    {{ $m->asset->barang->nama_barang ?? '-' }}
                                </td>
                                <td>{{ ucfirst($m->jenis) }}</td>
                                <td class="text-right">
                                    Rp {{ number_format($m->biaya, 0, ',', '.') }}
                                </td>
                                <td>
                                    @if($m->status == 'selesai')
                                        <span class="badge badge-success"><i class="fas fa-check-circle"></i> Selesai</span>
                                    @elseif($m->status == 'proses')
                                        <span class="badge badge-warning"><i class="fas fa-spinner fa-spin"></i> Proses</span>
                                    @else
                                        <span class="badge badge-secondary">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $m->tanggal_mulai ? $m->tanggal_mulai->format('d M Y') : '-' }}</td>
                                <td>{{ $m->tanggal_selesai ? $m->tanggal_selesai->format('d M Y') : '-' }}</td>
                                <td>
                                    @if($m->status != 'selesai')
                                        {{-- BUTTON EDIT --}}
                                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editMaintenanceModal{{ $m->id_maintenance }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        {{-- BUTTON DELETE --}}
                                        <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteMaintenanceModal{{ $m->id_maintenance }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <span class="text-muted"><i class="fas fa-lock"></i> Terkunci</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        @if ($maintenances->count() == 0)
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    Tidak ada data aset dalam antrean maintenance untuk periode ini.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                {{-- PAGINATION LINK SINKRON STRUKTUR --}}
                <div class="row mt-3">
                    <div class="col-md-6 text-muted">
                        Showing {{ $maintenances->firstItem() ?? 0 }} to {{ $maintenances->lastItem() ?? 0 }} of {{ $maintenances->total() }} entries
                    </div>
                    <div class="col-md-6 d-flex justify-content-end">
                        {{ $maintenances->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

{{-- JAVASCRIPT DATATABLES INTEGRATION --}}
<script>
    $(document).ready(function () {
        if (!$.fn.DataTable.isDataTable('.table')) {
            $('.table').DataTable({
                "paging": false,
                "info": false,
                "responsive": true,
                "autoWidth": false
            });
        }

        // Auto-submit form ketika user mengganti pilihan dropdown tanpa klik tombol cari
        $('.auto-filter').on('change', function() {
            $(this).closest('form').submit();
        });
    });
</script>

{{-- MODALS INCLUDE DIRECTORY --}}
@include('superadmin.aset.maintenance.modal-edit')
@include('superadmin.aset.maintenance.modal-delete')

@endsection