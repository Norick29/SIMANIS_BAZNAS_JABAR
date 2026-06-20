@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- Page Heading --}}
    <h1 class="h3 mb-2 text-gray-800">
        <i class="fas fa-wallet mr-2"></i> Data Sumber Dana
    </h1>

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

            @if ($type == 'warning' && session('duplicates'))
                <hr>
                <ul>
                    @foreach (session('duplicates') as $d)
                        <li>{{ $d }}</li>
                    @endforeach
                </ul>
            @endif

            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif
@endforeach

    {{-- Card --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <a href="#"
               class="btn btn-success btn-sm"
               data-toggle="modal"
               data-target="#createSumberDanaModal">
                <i class="fas fa-plus"></i> Tambah Sumber Dana
            </a>

            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-list"></i> Daftar Sumber Dana</h6>
        </div>

        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th>Kode Sumber Dana</th>
                            <th>Nama Sumber Dana</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($sumber as $s)
                        <tr class="text-center">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $s->kode_sumber_dana }}</td>
                            <td>{{ $s->nama_sumber_dana }}</td>
                            <td>{{ $s->created_at->format('d M Y') }}</td>
                            <td>{{ $s->updated_at->diffForHumans() }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm"
                                        data-toggle="modal"
                                        data-target="#editSumberModal{{ $s->id_sumber_dana }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button class="btn btn-danger btn-sm"
                                        data-toggle="modal"
                                        data-target="#deleteSumberModal{{ $s->id_sumber_dana }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                Data sumber dana belum tersedia
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
@include('superadmin.master.sumber.modal-create')
@include('superadmin.master.sumber.modal-edit')
@include('superadmin.master.sumber.modal-delete')

@endsection
