@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Manajemen Amil</h1> 

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2 ">
                <a href="#" class="btn btn-success btn-sm mr-2"
                data-toggle="modal"
                data-target="#createAmilModal">
                    <i class="fas fa-user-plus"></i> Tambah Amil
                </a>

                <a href="#" class="btn btn-info btn-sm"
                data-toggle="modal"
                data-target="#importAmilModal">
                    <i class="fas fa-file-excel"></i> Import Excel
                </a>
            </div>

            <h6 class="m-0 font-weight-bold text-success">Daftar Amil</h6>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>No</th>
                            <th>NIA</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Divisi</th>
                            <th>Departement</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($amil as $a)
                        <tr class="text-center">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $a->nia }}</td>
                            <td>{{ $a->nama }}</td>
                            <td>
                                <span class="badge badge-primary">
                                    {{ $a->jabatan->nama_jabatan ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $a->divisi->nama_divisi ?? '-' }}
                                </span>
                            </td>
                            <td>{{ $a->nama_dept ?? '-' }}</td>
                            <td>{{ $a->created_at->format('d M Y') }}</td>
                            <td>{{ $a->updated_at->diffForHumans() }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm"
                                    data-toggle="modal"
                                    data-target="#editAmilModal{{ $a->id_amil }}">
                                    <i class="fas fa-edit"></i>
                                </button>


                                <button class="btn btn-danger btn-sm"
                                    data-toggle="modal"
                                    data-target="#deleteAmilModal{{ $a->id_amil }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach

                        @if ($amil->count() == 0)
                        <tr>
                            <td colspan="9" class="text-center text-muted">
                                Data amil belum tersedia
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                <div class="row mt-3">
                    <div class="col-md-6 text-muted">
                        Showing {{ $amil->firstItem() }} to {{ $amil->lastItem() }}
                        of {{ $amil->total() }} entries
                    </div>

                    <div class="col-md-6 d-flex justify-content-end">
                        {{ $amil->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.table').DataTable();
    });
</script>

@include('superadmin.master.amil.modal-create')
@include('superadmin.master.amil.modal-import')
@include('superadmin.master.amil.modal-edit')
@include('superadmin.master.amil.modal-delete')

@endsection
