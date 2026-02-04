@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <h1 class="h3 mb-2 text-gray-800">Data Jenis</h1>

    {{-- VALIDATION ERRORS --}}
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


    <div class="card shadow mb-4">

        {{-- CARD HEADER --}}
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div>
                <button class="btn btn-success btn-sm"
                        data-toggle="modal"
                        data-target="#createJenisModal">
                    <i class="fas fa-plus"></i> Tambah Jenis
                </button>

                <a href="#" class="btn btn-info btn-sm"
                data-toggle="modal"
                data-target="#importJenisModal">
                    <i class="fas fa-file-excel"></i> Import Excel
                </a>
            </div>
            <h6 class="m-0 font-weight-bold text-success">Daftar Jenis</h6>
        </div>

        {{-- CARD BODY --}}
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%">
                    <thead class="bg-light">
                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th>Kode Jenis</th>
                            <th>Nama Jenis</th>
                            <th width="20%">Created At</th>
                            <th width="20%">Updated At</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jenis as $j)
                            <tr class="text-center">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $j->kode_jenis }}</td>
                                <td>{{ $j->nama_jenis }}</td>
                                <td>{{ $j->created_at->format('d M Y') }}</td>
                                <td>{{ $j->updated_at->diffForHumans() }}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm"
                                        data-toggle="modal"
                                        data-target="#editJenisModal{{ $j->id_jenis }}">
                                        <i class="fas fa-edit"></i>
                                    </button>


                                    <button class="btn btn-danger btn-sm"
                                        data-toggle="modal"
                                        data-target="#deleteJenisModal{{ $j->id_jenis }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    Data jenis belum tersedia
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- PAGINATION --}}
                <div class="row mt-3">
                    <div class="col-md-6 text-muted">
                        Showing {{ $jenis->firstItem() }} to {{ $jenis->lastItem() }}
                        of {{ $jenis->total() }} entries
                    </div>

                    <div class="col-md-6 d-flex justify-content-end">
                        {{ $jenis->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@include('superadmin.master.jenis.modal-create')
@include('superadmin.master.jenis.modal-import')
@include('superadmin.master.jenis.modal-edit')
@include('superadmin.master.jenis.modal-delete')
@endsection
