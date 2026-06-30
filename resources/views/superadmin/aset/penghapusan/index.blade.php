@extends('layouts.app')

@section('title', 'Data Penghapusan Aset')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><i class="fas fa-trash mr-2 text-secondary"></i>Riwayat Penghapusan Aset</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2">
                <button class="btn btn-secondary btn-sm animate-button" type="button" data-toggle="collapse" data-target="#filterAsset">
                    <i class="fas fa-filter"></i> Filter Periode
                </button>
            </div>
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-list"></i> Daftar Riwayat - Periode {{ $monthsList[$selectedMonth] }} {{ $selectedYear }}
            </h6>
        </div>

        {{-- CONTAINER TOGGLE COLLAPSE FILTER --}}
        <div class="collapse {{ request()->hasAny(['bulan', 'tahun', 'status', 'id_lokasi']) ? 'show' : '' }}" id="filterAsset">
            <div class="card border-left-success shadow-sm m-3">
                <div class="card-body bg-light">
                    <form method="GET" action="{{ url()->current() }}">
                        {{-- Bawa serta parameter search navbar jika ada --}}
                        @if(request()->filled('q'))
                            <input type="hidden" name="q" value="{{ request('q') }}">
                        @endif

                        <div class="row">
                            {{-- FILTER BULAN --}}
                            <div class="col-md-3">
                                <label class="font-weight-bold text-gray-800">Bulan Log</label>
                                <select name="bulan" class="form-control form-control-sm">
                                    @foreach($monthsList as $num => $name)
                                        <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- FILTER TAHUN --}}
                            <div class="col-md-2">
                                <label class="font-weight-bold text-gray-800">Tahun</label>
                                <select name="tahun" class="form-control form-control-sm">
                                    @foreach($yearsList as $yr)
                                        <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>
                                            {{ $yr }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- FILTER STATUS --}}
                            <div class="col-md-3">
                                <label class="font-weight-bold text-gray-800">Status Approval</label>
                                <select name="status" class="form-control form-control-sm">
                                    <option value="">Semua Status</option>
                                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>

                            {{-- FILTER LOKASI ASAL ASET --}}
                            <div class="col-md-4">
                                <label class="font-weight-bold text-gray-800">Lokasi Terakhir Aset</label>
                                <select name="id_lokasi" class="form-control form-control-sm">
                                    <option value="">Semua Lokasi</option>
                                    @foreach($lokasis as $l)
                                        <option value="{{ $l->id_lokasi }}" {{ request('id_lokasi') == $l->id_lokasi ? 'selected' : '' }}>
                                            {{ $l->nama_lokasi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-3 text-right">
                            <a href="{{ url()->current() }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-undo"></i> Reset
                            </a>
                            <button type="submit" class="btn btn-sm btn-success">
                                <i class="fas fa-search"></i> Terapkan Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Kode Aset</th>
                            <th>Nama Barang</th>
                            <th>Lokasi Terakhir</th>
                            <th>Alasan Penghapusan</th>
                            <th>Diajukan Oleh</th>
                            <th width="12%">Status</th>
                            <th>Tanggal Action</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($penghapusans as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="badge badge-secondary font-weight-bold">
                                    {{ $item->asset->kode_aset ?? '-' }}
                                </span>
                            </td>
                            <td>{{ $item->asset->barang->nama_barang ?? '-' }}</td>
                            <td>{{ $item->asset->lokasi->nama_lokasi ?? '-' }}</td>
                            <td>
                                <small class="text-muted d-block" style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $item->alasan }}">
                                    {{ $item->alasan }}
                                </small>
                            </td>
                            <td>{{ $item->requestedBy->name ?? '-' }}</td>
                            <td>
                                @if($item->status == 'menunggu')
                                    <span class="badge badge-warning p-2 d-block">
                                        <i class="fas fa-clock mr-1"></i> Menunggu
                                    </span>
                                @elseif($item->status == 'disetujui')
                                    <span class="badge badge-success p-2 d-block">
                                        <i class="fas fa-check-circle mr-1"></i> Disetujui
                                    </span>
                                @else
                                    <span class="badge badge-danger p-2 d-block">
                                        <i class="fas fa-times-circle mr-1"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                            <td>{{ $item->created_at->format('d M Y') }}</td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('penghapusan.show', $item->id_penghapusan) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($item->status == 'disetujui')
                                        <a href="{{ route('penghapusan.cetak-ba', $item->id_penghapusan) }}" 
                                           class="btn btn-sm btn-primary" 
                                           target="_blank"
                                           title="Cetak Berita Acara">
                                            <i class="fas fa-file-pdf"></i> BA
                                        </a>
                                    @else
                                        <button class="btn btn-sm btn-secondary" disabled title="Berita acara hanya untuk yang disetujui">
                                            <i class="fas fa-file-pdf"></i> BA
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="fas fa-folder-open fa-2x d-block mb-2"></i>
                                Belum ada data riwayat penghapusan aset.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection