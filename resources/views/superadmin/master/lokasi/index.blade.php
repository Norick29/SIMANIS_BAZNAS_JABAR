@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <h1 class="h3 mb-2 text-gray-800">Data Lokasi</h1>

    {{-- ALERT --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">

        {{-- CARD HEADER --}}
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div>
                <button class="btn btn-success btn-sm"
                        data-toggle="modal"
                        data-target="#createLokasiModal">
                    <i class="fas fa-plus"></i> Tambah Lokasi
                </button>

                <a href="#" class="btn btn-info btn-sm"
                data-toggle="modal"
                data-target="#importLokasiModal">
                    <i class="fas fa-file-excel"></i> Import Excel
                </a>
            </div>
            <h6 class="m-0 font-weight-bold text-success">Daftar Lokasi</h6>
        </div>

        {{-- CARD BODY --}}
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%">
                    <thead class="bg-light">
                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th>Kode Lokasi</th>
                            <th>Nama Lokasi</th>
                            <th width="20%">Created At</th>
                            <th width="20%">Updated At</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lokasis as $lokasi)
                            <tr class="text-center">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $lokasi->kode_lokasi }}</td>
                                <td>{{ $lokasi->nama_lokasi }}</td>
                                <td>{{ $lokasi->created_at->format('d M Y') }}</td>
                                <td>{{ $lokasi->updated_at->diffForHumans() }}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm"
                                        data-toggle="modal"
                                        data-target="#editLokasiModal{{ $lokasi->id_lokasi }}">
                                        <i class="fas fa-edit"></i>
                                    </button>


                                    <button class="btn btn-danger btn-sm"
                                        data-toggle="modal"
                                        data-target="#deleteLokasiModal{{ $lokasi->id_lokasi }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    Data lokasi belum tersedia
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- PAGINATION --}}
                <div class="row mt-3">
                    <div class="col-md-6 text-muted">
                        Showing {{ $lokasis->firstItem() }} to {{ $lokasis->lastItem() }}
                        of {{ $lokasis->total() }} entries
                    </div>

                    <div class="col-md-6 d-flex justify-content-end">
                        {{ $lokasis->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@include('superadmin.master.lokasi.modal-create')
@include('superadmin.master.lokasi.modal-import')
@include('superadmin.master.lokasi.modal-edit')
@include('superadmin.master.lokasi.modal-delete')
@endsection
