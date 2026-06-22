@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <h1 class="h3 mb-2 text-gray-800">
        <i class="fas fa-users mr-2"></i> Manajemen User
    </h1>

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
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif
    @endforeach

    {{-- DATA CARD --}}
    <div class="card shadow mb-4">

        {{-- CARD HEADER --}}
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div>
                <button class="btn btn-success btn-sm"
                        data-toggle="modal"
                        data-target="#createUserModal">
                    <i class="fas fa-plus"></i> Tambah User
                </button>
            </div>
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-list"></i> Daftar User
            </h6>
        </div>

        {{-- CARD BODY --}}
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%">
                    <thead class="bg-light">
                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>No HP</th>
                            <th>Role</th>
                            <th width="15%">Digital Signature</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $u)
                            <tr class="text-center">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-left"><strong>{{ $u->name }}</strong></td>
                                <td class="text-left">{{ $u->email }}</td>
                                <td>{{ $u->no_hp }}</td>
                                <td>
                                    @if ($u->role == 'super admin')
                                        <span class="badge badge-danger">Super Admin</span>
                                    @elseif ($u->role == 'kadiv')
                                        <span class="badge badge-primary">Kepala Divisi</span>
                                    @elseif ($u->role == 'kadept')
                                        <span class="badge badge-info">Kepala Departement</span>
                                    @else
                                        <span class="badge badge-secondary">Staf</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($u->role == 'kadiv' || $u->role == 'kadept')
                                        @if ($u->public_key)
                                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> Ada RSA Key</span>
                                        @else
                                            <span class="badge badge-warning"><i class="fas fa-exclamation-circle"></i> Belum Ada</span>
                                        @endif
                                    @else
                                        <span class="badge badge-light text-muted"><i class="fas fa-ban"></i> N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('users.edit', $u->id_user) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- <form action="{{ route('users.destroy', $u->id_user) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user {{ $u->name }}? Data tidak dapat dikembalikan!');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form> --}}
                                    <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteUser{{ $u->id_user }}" data-id="{{ $u->id_user }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    Data user belum tersedia
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- PAGINATION (Menggunakan struktur pagination referensimu) --}}
                <div class="row mt-3">
                    <div class="col-md-6 text-muted">
                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }}
                        of {{ $users->total() }} entries
                    </div>

                    <div class="col-md-6 d-flex justify-content-end">
                        {{ $users->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
</div>

{{-- MODALS INCLUDE (Diposisikan di paling luar container, persis seperti contoh aman) --}}
@include('superadmin.user.modal-create')
@include('superadmin.user.modal-delete')

@endsection