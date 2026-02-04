@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Manajemen Jabatan</h1> 

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>

    @elseif (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <a href="#" class="btn btn-success btn-sm"
               data-toggle="modal"
               data-target="#createJabatanModal">
                <i class="fas fa-plus"></i> Tambah Jabatan
            </a>
            <h6 class="m-0 font-weight-bold text-success">Daftar Jabatan</h6>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>No</th>
                            <th>Nama Jabatan</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($jabatan as $j)
                        <tr class="text-center">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $j->nama_jabatan }}</td>
                            <td>{{ $j->created_at->format('d M Y') }}</td>
                            <td>{{ $j->updated_at->diffForHumans() }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm"
                                    data-toggle="modal"
                                    data-target="#editJabatanModal{{ $j->id_jabatan }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger btn-sm"
                                    data-toggle="modal"
                                    data-target="#deleteJabatanModal{{ $j->id_jabatan }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach

                        @if ($jabatan->count() == 0)
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                Data jabatan belum tersedia
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('.table').DataTable();
});
</script>

@include('superadmin.master.jabatan.modal-create')
@include('superadmin.master.jabatan.modal-edit')
@include('superadmin.master.jabatan.modal-delete')

@endsection
