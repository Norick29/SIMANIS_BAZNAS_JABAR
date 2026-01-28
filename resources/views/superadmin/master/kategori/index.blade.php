@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- Page Heading --}}
    <h1 class="h3 mb-2 text-gray-800">Manajemen Kategori</h1>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    {{-- Card --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <a href="#"
               class="btn btn-success btn-sm"
               data-toggle="modal"
               data-target="#createKategoriModal">
                <i class="fas fa-plus"></i> Tambah Kategori
            </a>

            <h6 class="m-0 font-weight-bold text-success">Daftar Kategori</h6>
        </div>

        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th>Kode Kategori</th>
                            <th>Nama Kategori</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($kategori as $k)
                        <tr class="text-center">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $k->kode_kategori }}</td>
                            <td>{{ $k->nama_kategori }}</td>
                            <td>{{ $k->created_at }}</td>
                            <td>{{ $k->updated_at }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm" disabled>
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button class="btn btn-danger btn-sm" disabled>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                Data kategori belum tersedia
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

{{-- Modal --}}
@include('superadmin.master.kategori.modal-create')

@endsection
