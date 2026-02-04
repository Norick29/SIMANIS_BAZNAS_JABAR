@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Manajemen Aset</h1>

    {{-- ERROR --}}
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
            <div class="d-flex gap-2">
                <a href="#" class="btn btn-success btn-sm mr-2"
                data-toggle="modal"
                data-target="#createAssetModal">
                    <i class="fas fa-plus"></i> Tambah Asset
                </a>

                <a href="#" class="btn btn-info btn-sm"
                   data-toggle="modal"
                   data-target="#importAssetModal">
                    <i class="fas fa-file-excel"></i> Import Excel
                </a>
            </div>

            <h6 class="m-0 font-weight-bold text-success">Daftar Aset</h6>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>No</th>
                            <th>Kode Aset</th>
                            <th>Barang</th>
                            <th>Kategori</th>
                            <th>Jenis</th>
                            <th>Lokasi</th>
                            <th>Amil</th>
                            <th>Sumber Dana</th>
                            <th>Kondisi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($assets as $aset)
                        <tr class="text-center">
                            <td>{{ $assets->firstItem() + $loop->index }}</td>
                            <td class="font-weight-bold text-success">
                                {{ $aset->kode_aset }}
                            </td>
                            <td>{{ $aset->barang->nama_barang ?? '-' }}</td>
                            <td>
                                <span class="badge badge-primary">
                                    {{ $aset->kategori->nama_kategori ?? '-' }}
                                </span>
                            </td>
                            <td>{{ $aset->jenis->nama_jenis ?? '-' }}</td>
                            <td>{{ $aset->lokasi->nama_lokasi ?? '-' }}</td>
                            <td>{{ $aset->amil->nama ?? '-' }}</td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $aset->sumberDana->nama_sumber_dana ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $aset->kondisi == 'baik' ? 'success' : ($aset->kondisi == 'rusak' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($aset->kondisi) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-secondary">
                                    {{ ucfirst($aset->status) }}
                                </span>
                            </td>
                            <td>
                                {{-- DETAIL --}}
                                <a href="#"
                                   class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>

                                {{-- EDIT --}}
                                <a href="#"
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- DELETE --}}
                                <button class="btn btn-danger btn-sm"
                                    data-toggle="modal"
                                    data-target="#deleteAssetModal{{ $aset->id_aset }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted">
                                Data aset belum tersedia
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- PAGINATION --}}
                <div class="row mt-3">
                    <div class="col-md-6 text-muted">
                        Showing {{ $assets->firstItem() }} to {{ $assets->lastItem() }}
                        of {{ $assets->total() }} entries
                    </div>

                    <div class="col-md-6 d-flex justify-content-end">
                        {{ $assets->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- @include('assets.modal-import')
@include('assets.modal-delete') --}}
@include('superadmin.aset.data_aset.modal-create')

@endsection
