@extends('layouts.app')

@section('content')

<div class="container-fluid">
    {{-- Page Heading --}}
    <h1 class="h3 mb-2 text-gray-800">User Management</h1> 

    {{-- Succes Massage --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Card --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <a href="#" class="btn btn-success btn-sm" data-toggle="modal" data-target="#createUserModal">
                <i class="fas fa-user-plus"></i> Add User
            </a>
            <h6 class="m-0 font-weight-bold text-success">Users List</h6>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th width="15%">Role</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th width="15%">Actions</th>
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
                                @switch($user->role)
                                    @case('super admin')
                                        <span class="badge badge-primary">Super Admin</span>
                                        @break
                                    @case('kadiv')
                                        <span class="badge badge-success">Kepala Divisi</span>
                                        @break
                                    @case('kadept')
                                        <span class="badge badge-info">Kepala Departement</span>
                                        @break
                                    @default
                                        <span class="badge badge-secondary">Staf</span>
                                @endswitch
                            </td>
                            <td>{{ $user->created_at }}</td>
                            <td>{{ $user->updated_at }}</td>
                            <td class="text-center">
                                <button class="btn btn-warning btn-sm"
                                        data-toggle="modal"
                                        data-target="#editUserModal{{ $user->id_user }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                @if(auth()->user()->id_user != $user->id_user)
                                    <button class="btn btn-danger btn-sm"
                                            data-toggle="modal"
                                            data-target="#deleteConfirmModal"
                                            data-action="{{ route('users.destroy', $user->id_user) }}"
                                            data-name="{{ $user->name }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach

                        @if ($users->count() == 0)
                        <tr>
                            <td colspan="5" class="text-center text-muted">No users found.</td>
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

@include('superadmin.user.modal-create')
@include('superadmin.user.modal-edit')
@include('superadmin.user.modal-delete')


@endsection