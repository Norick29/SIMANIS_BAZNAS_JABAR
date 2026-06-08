@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">
        Riwayat Perpindahan Aset
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

    <div class="card shadow mb-4">

        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Daftar Riwayat Perpindahan Aset
            </h6>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered">

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
                                    {{ $loop->iteration }}
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
                                        <strong>Lokasi:</strong>
                                        {{ $item->asset->lokasi->nama_lokasi ?? '-' }}

                                        <br>

                                        <strong>PJ:</strong>
                                        {{ $item->amilSebelum->nama ?? '-' }}
                                    </small>
                                </td>

                                <td>
                                    <small>
                                        <strong>Lokasi:</strong>
                                        {{ $item->lokasiBaru->nama_lokasi ?? '-' }}

                                        <br>

                                        <strong>PJ:</strong>
                                        {{ $item->amilSesudah->nama ?? '-' }}
                                    </small>
                                </td>

                                <td class="text-center">
                                    {{ $item->requestedBy->name ?? '-' }}
                                </td>

                                <td class="text-center">

                                    @if ($item->status == 'menunggu')

                                        <span class="badge badge-warning">
                                            Menunggu
                                        </span>

                                    @elseif ($item->status == 'disetujui')

                                        <span class="badge badge-success">
                                            Disetujui
                                        </span>

                                    @else

                                        <span class="badge badge-danger">
                                            Ditolak
                                        </span>

                                    @endif

                                </td>

                                <td class="text-center">
                                    {{ $item->created_at->format('d M Y H:i') }}
                                </td>

                                <td class="text-center">

                                    <button
                                        class="btn btn-info btn-sm"
                                        data-toggle="modal"
                                        data-target="#detailModal{{ $item->id_pindah }}"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    @if($item->status == 'disetujui')

                                        <a href="{{ route('berita-acara.show', $item->id_pindah) }}"
                                        class="btn btn-success btn-sm">
                                            <i class="fas fa-file-alt"></i>
                                        </a>

                                        <a href="{{ route('berita-acara.pdf', $item->id_pindah) }}"
                                        class="btn btn-danger btn-sm">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    Belum ada riwayat perpindahan aset
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

                <div class="row mt-3">

                    <div class="col-md-6 text-muted">

                        Showing
                        {{ $riwayat->firstItem() }}
                        to
                        {{ $riwayat->lastItem() }}

                        of
                        {{ $riwayat->total() }}
                        entries

                    </div>

                    <div class="col-md-6 d-flex justify-content-end">
                        {{ $riwayat->links('pagination::bootstrap-4') }}
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

{{-- MODAL DETAIL --}}
@foreach ($riwayat as $item)

<div class="modal fade" id="detailModal{{ $item->id_pindah }}" tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header bg-info text-white">

                <h5 class="modal-title">
                    Detail Riwayat Perpindahan
                </h5>

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
                        <th>Alasan</th>
                        <td>{{ $item->alasan }}</td>
                    </tr>

                    <tr>
                        <th>Status</th>
                        <td>

                            @if ($item->status == 'menunggu')

                                <span class="badge badge-warning">
                                    Menunggu
                                </span>

                            @elseif ($item->status == 'disetujui')

                                <span class="badge badge-success">
                                    Disetujui
                                </span>

                            @else

                                <span class="badge badge-danger">
                                    Ditolak
                                </span>

                            @endif

                        </td>
                    </tr>

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

@endsection