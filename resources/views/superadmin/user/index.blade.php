@extends('layouts.app')

@section('content')
<div class="container-fluid pb-">

    {{-- Page Heading --}}


    <h1 class="h3 mb-2 text-gray-800">Manajemen User</h1>

    {{-- Alert --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#createUserModal">
                <i class="fas fa-user-plus"></i> Tambah User
            </button>
            <h6 class="m-0 font-weight-bold text-success">Daftar Pengguna</h6>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>No HP</th>
                            <th>Role</th>
                            <th>Created</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                        <tr class="text-center">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->no_hp }}</td>
                            <td>
                                <span class="badge badge-info text-uppercase">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td>{{ optional($user->created_at)->format('d-m-Y') ?? '-' }}</td>

                            <td>
                                <button class="btn btn-warning btn-sm"
                                        data-toggle="modal"
                                        data-target="#editUserModal{{ $user->id_user }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                @if (auth()->id() != $user->id_user)
                                <button class="btn btn-danger btn-sm"
                                        data-toggle="modal"
                                        data-target="#deleteUserModal{{ $user->id_user }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@include('superadmin.user.modal-create')
@include('superadmin.user.modal-edit')
@include('superadmin.user.modal-delete')

@endsection
