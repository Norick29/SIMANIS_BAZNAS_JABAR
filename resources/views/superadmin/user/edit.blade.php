@extends('layouts.app') {{-- Sesuaikan dengan nama layout kamu --}}

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-warning text-dark">
                    <h5 class="card-title mb-0">
                        {{-- PERBAIKAN: $users->name menjadi $user->name --}}
                        <i class="fas fa-user-edit mr-2"></i> Edit User: {{ $user->name }}
                    </h5>
                </div>
                
                {{-- PERBAIKAN: $users->id_user menjadi $user->id_user --}}
                <form action="{{ route('users.update', $user->id_user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="row">
                            {{-- NAMA --}}
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label><strong>Full Name</strong></label>
                                    {{-- PERBAIKAN: $users->name menjadi $user->name --}}
                                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                </div>
                            </div>

                            {{-- EMAIL --}}
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label><strong>Email</strong></label>
                                    {{-- PERBAIKAN: $users->email menjadi $user->email --}}
                                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                </div>
                            </div>

                            {{-- NO HP --}}
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label><strong>Phone Number</strong></label>
                                    {{-- PERBAIKAN: $users->no_hp menjadi $user->no_hp --}}
                                    <input type="text" name="no_hp" class="form-control" value="{{ $user->no_hp }}" required>
                                </div>
                            </div>

                            {{-- ROLE --}}
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label><strong>Role</strong></label>
                                    <select name="role" class="form-control" required>
                                        {{-- PERBAIKAN: Semua $users->role menjadi $user->role --}}
                                        <option value="super admin" {{ $user->role == 'super admin' ? 'selected' : '' }}>Super Admin</option>
                                        <option value="kadiv" {{ $user->role == 'kadiv' ? 'selected' : '' }}>Kepala Divisi</option>
                                        <option value="kadept" {{ $user->role == 'kadept' ? 'selected' : '' }}>Kepala Departement</option>
                                        <option value="staf" {{ $user->role == 'staf' ? 'selected' : '' }}>Staf</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-end gap-2">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary mr-2">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection