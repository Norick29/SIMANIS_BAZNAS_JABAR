@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">
        <i class="fas fa-history mr-2 text-secondary"></i> Riwayat Perpindahan Aset
    </h1>

    {{-- SESSION ALERT --}}
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

    {{-- CARD UTAMA --}}
    <div class="card shadow mb-4">
        {{-- HEADER CARD --}}
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2">
                <button class="btn btn-secondary btn-sm" type="button" data-toggle="collapse" data-target="#filterAsset">
                    <i class="fas fa-filter"></i> Filter Periode
                </button>
            </div>
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-list"></i> Daftar Riwayat - Periode {{ $monthsList[$selectedMonth] }} {{ $selectedYear }}
            </h6>
        </div>

        {{-- CONTAINER TOGGLE COLLAPSE FILTER (Rapi di luar card-body agar tabel tidak renggang) --}}
        <div class="collapse {{ request()->hasAny(['bulan', 'tahun', 'status', 'id_lokasi_baru']) ? 'show' : '' }}" id="filterAsset">
            <div class="card border-left-success shadow-sm mb-3">
                <div class="card-body bg-light">
                    <form method="GET" action="{{ url()->current() }}">
                        {{-- Bawa serta parameter search navbar jika ada --}}
                        @if(request()->filled('q'))
                            <input type="hidden" name="q" value="{{ request('q') }}">
                        @endif

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
                            <div class="col-md-2">
                                <label class="font-weight-bold">Tahun</label>
                                <select name="tahun" class="form-control auto-filter">
                                    @foreach($yearsList as $yr)
                                        <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>
                                            {{ $yr }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- FILTER STATUS --}}
                            <div class="col-md-3">
                                <label class="font-weight-bold">Status</label>
                                <select name="status" class="form-control auto-filter">
                                    <option value="">Semua Status</option>
                                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>

                            {{-- FILTER LOKASI BARU / TUJUAN --}}
                            <div class="col-md-4">
                                <label class="font-weight-bold">Lokasi Tujuan (Baru)</label>
                                <select name="id_lokasi_baru" class="form-control auto-filter">
                                    <option value="">Semua Lokasi</option>
                                    @foreach($lokasis as $l)
                                        <option value="{{ $l->id_lokasi }}" {{ request('id_lokasi_baru') == $l->id_lokasi ? 'selected' : '' }}>
                                            {{ $l->nama_lokasi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-3 text-right">
                            <a href="{{ url()->current() }}" class="btn btn-secondary">Reset</a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-search"></i> Terapkan Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- BODY CARD --}}
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>No</th>
                            <th>Kode Aset</th>
                            <th>Barang</th>
                            <th>Dari</th>
                            <th>Ke</th>
                            <th>Pemohon</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($riwayat as $item)
                            <tr>
                                <td class="text-center">
                                    {{ $riwayat->firstItem() + $loop->index }}
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info">
                                        {{ $item->asset->kode_aset ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    {{ $item->asset->barang->nama_barang ?? '-' }}
                                </td>
                                <td>
                                    <small>
                                        <strong>Lokasi:</strong> {{ $item->asset->lokasi->nama_lokasi ?? '-' }}<br>
                                        <strong>PJ:</strong> {{ $item->amilSebelum->nama ?? '-' }}
                                    </small>
                                </td>
                                <td>
                                    <small>
                                        <strong>Lokasi:</strong> {{ $item->lokasiBaru->nama_lokasi ?? '-' }}<br>
                                        <strong>PJ:</strong> {{ $item->amilSesudah->nama ?? '-' }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    {{ $item->requestedBy->name ?? '-' }}
                                </td>
                                <td class="text-center">
                                    @if ($item->status == 'menunggu')
                                        <span class="badge badge-warning">Menunggu</span>
                                    @elseif ($item->status == 'disetujui')
                                        <span class="badge badge-success">Disetujui</span>
                                    @else
                                        <span class="badge badge-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ $item->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="text-center">
                                    {{-- Semua Role boleh melihat detail modal --}}
                                    <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#detailModal{{ $item->id_pindah }}">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    {{-- Akses Dokumen jika sudah disetujui --}}
                                    @if($item->status == 'disetujui')
                                        <a href="{{ route('berita-acara.show', $item->id_pindah) }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-file-alt"></i>
                                        </a>
                                        <a href="{{ route('berita-acara.pdf', $item->id_pindah) }}" class="btn btn-danger btn-sm">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    @endif

                                    {{-- OPSI: Jika status masih menunggu, dan user yang login adalah Kadiv/Kadept --}}
                                    {{-- Anda bisa menampilkan tombol shortcut action di sini jika dibutuhkan --}}
                                    @if($item->status == 'menunggu' && in_array(auth()->user()->role, ['kadiv', 'kadept']))
                                        {{-- Contoh: Tombol Cepat Menuju Validasi --}}
                                        <span class="badge badge-warning d-block mt-1">Perlu Persetujuan</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    Tidak ada data riwayat perpindahan aset untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- PAGINATION LINK --}}
                <div class="row mt-3">
                    <div class="col-md-6 text-muted">
                        Showing {{ $riwayat->firstItem() ?? 0 }} to {{ $riwayat->lastItem() ?? 0 }} of {{ $riwayat->total() }} entries
                    </div>
                    <div class="col-md-6 d-flex justify-content-end">
                        {{ $riwayat->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DETAIL OVERLAY LOOP --}}
@foreach ($riwayat as $item)
    <div class="modal fade" id="detailModal{{ $item->id_pindah }}" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Detail Riwayat Perpindahan</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Kode Aset</th>
                            <td>{{ $item->asset->kode_aset ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Barang</th>
                            <td>{{ $item->asset->barang->nama_barang ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Lokasi Sebelum</th>
                            <td>{{ $item->asset->lokasi->nama_lokasi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Lokasi Sesudah</th>
                            <td>{{ $item->lokasiBaru->nama_lokasi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>PJ Sebelum</th>
                            <td>{{ $item->amilSebelum->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>PJ Sesudah</th>
                            <td>{{ $item->amilSesudah->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Alasan Pengajuan</th>
                            <td>{{ $item->alasan }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if ($item->status == 'menunggu')
                                    <span class="badge badge-warning">Menunggu</span>
                                @elseif ($item->status == 'disetujui')
                                    <span class="badge badge-success">Disetujui</span>
                                @else
                                    <span class="badge badge-danger">Ditolak</span>
                                @endif
                            </td>
                        </tr>

                        @if($item->status == 'rejected' || $item->status == 'ditolak')
                            @php
                                $rejectedApproval = $item->approvals->where('status', 'ditolak')->first();
                            @endphp
                            @if($rejectedApproval)
                                <tr class="table-danger text-dark">
                                    <th>Ditolak Pada Stage</th>
                                    <td class="font-weight-bold">
                                        {{ $rejectedApproval->stage === 1 ? 'Stage 1 - Kepala Departemen (KADEPT)' : 'Stage 2 - Kepala Divisi (KADIV)' }}
                                    </td>
                                </tr>
                                <tr class="table-danger text-dark">
                                    <th>Alasan Penolakan Pimpinan</th>
                                    <td class="font-weight-bold text-danger">
                                        {{ $rejectedApproval->note ?? 'Alasan tidak diisi oleh pimpinan.' }}
                                    </td>
                                </tr>
                            @endif
                        @endif
                        <tr>
                            <th>Dibuat Pada</th>
                            <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endforeach

{{-- AUTO SUBMIT DROPDOWN JAVASCRIPT --}}
<script>
    $(document).ready(function () {
        $('.auto-filter').on('change', function() {
            $(this).closest('form').submit();
        });
    });
</script>
@endsection