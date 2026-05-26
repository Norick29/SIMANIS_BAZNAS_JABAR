@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Page Heading --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users"></i> Manajemen User
        </h1>
        <button type="button" 
                class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"
                data-toggle="modal" 
                data-target="#createUserModal">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah User
        </button>
    </div>

    {{-- Alert Success --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    {{-- Alert Error --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    {{-- Data Table Card --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Daftar User
            </h6>
        </div>

        <div class="card-body">
            @if ($users->isEmpty())
                <div class="alert alert-info text-center">
                    <i class="fas fa-inbox"></i> Tidak ada user yang terdaftar
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 5%">No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>No HP</th>
                                <th>Role</th>
                                <th style="width: 15%">Digital Signature</th>
                                <th style="width: 15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $key => $user)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <strong>{{ $user->name }}</strong>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->no_hp }}</td>
                                    <td>
                                        @if ($user->role == 'super admin')
                                            <span class="badge badge-danger">Super Admin</span>
                                        @elseif ($user->role == 'kadiv')
                                            <span class="badge badge-primary">Kepala Divisi</span>
                                        @elseif ($user->role == 'kadept')
                                            <span class="badge badge-info">Kepala Departement</span>
                                        @else
                                            <span class="badge badge-secondary">Staf</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($user->role == 'kadiv' || $user->role == 'kadept')
                                            @if ($user->public_key)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle"></i> Ada RSA Key
                                                </span>
                                            @else
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-exclamation-circle"></i> Belum Ada
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge badge-light text-muted">
                                                <i class="fas fa-ban"></i> N/A
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-warning btn-sm"
                                            data-toggle="modal"
                                            data-target="#editUser{{ $user->id_user }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>

                                        <button class="btn btn-danger btn-sm"
                                            data-toggle="modal"
                                            data-target="#deleteUser{{ $user->id_user }}">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>

                                        @if (($user->role == 'kadiv' || $user->role == 'kadept') && $user->public_key)
                                            <a href="{{ route('users.download-private-key', $user->id_user) }}"
                                               class="btn btn-sm btn-success"
                                               title="Download Private Key">
                                                <i class="fas fa-download"></i> Key
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>

{{-- INCLUDE MODALS (seperti struktur amil) --}}
@include('superadmin.user.modal-create')
@include('superadmin.user.modal-edit')
@include('superadmin.user.modal-delete')

<script>
    $(document).ready(function () {
        $('.table').DataTable();
    });
</script>

@endsection