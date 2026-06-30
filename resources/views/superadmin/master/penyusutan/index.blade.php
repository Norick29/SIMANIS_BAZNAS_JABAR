@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <h1 class="h3 mb-2 text-gray-800">
        <i class="fas fa-chart-line mr-2"></i>
        Master Kebijakan Penyusutan Aset
    </h1>

    {{-- VALIDATION ERROR --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

            <button class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    {{-- SESSION --}}
    @foreach (['success','error'] as $type)
        @if(session($type))
            <div class="alert alert-{{ $type == 'error' ? 'danger' : 'success' }} alert-dismissible fade show">

                {{ session($type) }}

                <button class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>

            </div>
        @endif
    @endforeach


    <div class="card shadow mb-4">

        {{-- HEADER CARD --}}
        <div class="card-header py-3 d-flex justify-content-between align-items-center">

            <div>
                <button
                    class="btn btn-success btn-sm"
                    data-toggle="modal"
                    data-target="#createMasterPenyusutanModal">

                    <i class="fas fa-plus"></i>
                    Tambah Kebijakan

                </button>
            </div>

            <h6 class="m-0 font-weight-bold text-success">

                <i class="fas fa-list"></i>

                Daftar Kebijakan Penyusutan

            </h6>

        </div>


        {{-- BODY CARD --}}
        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover">

                    <thead class="bg-light">

                        <tr class="text-center">

                            <th width="5%">No</th>

                            <th>Jenis</th>

                            <th>Umur Ekonomis</th>

                            <th>Persentase</th>

                            <th>Status</th>

                            <th width="15%">Aksi</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($masterPenyusutan as $item)

                            <tr>

                                <td class="text-center">
                                    {{ $loop->iteration }}
                                </td>

                                <td>

                                    <strong>

                                        {{ $item->jenis->nama_jenis }}

                                    </strong>

                                </td>

                                <td class="text-center">

                                    {{ $item->umur_ekonomis }}

                                    Tahun

                                </td>

                                <td class="text-center">

                                    {{ rtrim(rtrim(number_format($item->persentase,2),0),'.') }}

                                    %

                                </td>

                                <td class="text-center">

                                    @if($item->status=='aktif')

                                        <span class="badge badge-success">

                                            Aktif

                                        </span>

                                    @else

                                        <span class="badge badge-danger">

                                            Nonaktif

                                        </span>

                                    @endif

                                </td>

                                <td class="text-center">

                                    <button

                                        class="btn btn-warning btn-sm"

                                        data-toggle="modal"

                                        data-target="#editMasterPenyusutanModal{{ $item->id_master_penyusutan }}">

                                        <i class="fas fa-edit"></i>

                                    </button>

                                    <button

                                        class="btn btn-danger btn-sm"

                                        data-toggle="modal"

                                        data-target="#deleteMasterPenyusutanModal{{ $item->id_master_penyusutan }}">

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="text-center text-muted">

                                    Belum terdapat data kebijakan penyusutan.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

                {{-- PAGINATION --}}
                <div class="row mt-3">

                    <div class="col-md-6 text-muted">

                        Showing

                        {{ $masterPenyusutan->firstItem() }}

                        to

                        {{ $masterPenyusutan->lastItem() }}

                        of

                        {{ $masterPenyusutan->total() }}

                        entries

                    </div>

                    <div class="col-md-6 d-flex justify-content-end">

                        {{ $masterPenyusutan->links('pagination::bootstrap-4') }}

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


@include('superadmin.master.penyusutan.modal-create')

@include('superadmin.master.penyusutan.modal-edit')

@include('superadmin.master.penyusutan.modal-delete')

@endsection