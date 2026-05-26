@foreach ($users as $user)
<div class="modal fade"
     id="editUser{{ $user->id_user }}"
     tabindex="-1"
     role="dialog"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg">

            {{-- HEADER --}}
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-user-edit mr-2"></i> Edit User
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            {{-- FORM --}}
            <form action="{{ route('users.update', $user->id_user) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- BODY --}}
                <div class="modal-body">
                    <div class="row">

                        {{-- NAMA --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Full Name</strong></label>
                                <input type="text"
                                       name="name"
                                       class="form-control"
                                       value="{{ $user->name }}"
                                       required>
                            </div>
                        </div>

                        {{-- EMAIL --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Email</strong></label>
                                <input type="email"
                                       name="email"
                                       class="form-control"
                                       value="{{ $user->email }}"
                                       required>
                            </div>
                        </div>

                        {{-- NO HP --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Phone Number</strong></label>
                                <input type="text"
                                       name="no_hp"
                                       class="form-control"
                                       value="{{ $user->no_hp }}"
                                       required>
                            </div>
                        </div>

                        {{-- ROLE --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Role</strong></label>
                                <select name="role" class="form-control" required>
                                    <option value="super admin" {{ $user->role == 'super admin' ? 'selected' : '' }}>
                                        Super Admin
                                    </option>
                                    <option value="kadiv" {{ $user->role == 'kadiv' ? 'selected' : '' }}>
                                        Kepala Divisi
                                    </option>
                                    <option value="kadept" {{ $user->role == 'kadept' ? 'selected' : '' }}>
                                        Kepala Departement
                                    </option>
                                    <option value="staf" {{ $user->role == 'staf' ? 'selected' : '' }}>
                                        Staf
                                    </option>
                                </select>
                            </div>
                        </div>

                        {{-- PASSWORD --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Password (Kosongkan jika tidak ingin mengubah)</strong></label>
                                <input type="password"
                                       name="password"
                                       class="form-control"
                                       placeholder="Masukkan password baru (opsional)">
                            </div>
                        </div>

                        {{-- PASSWORD CONFIRMATION --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Confirm Password</strong></label>
                                <input type="password"
                                       name="password_confirmation"
                                       class="form-control"
                                       placeholder="Konfirmasi password (opsional)">
                            </div>
                        </div>

                        {{-- RSA KEY STATUS (untuk kadiv & kadept) --}}
                        @if ($user->role == 'kadiv' || $user->role == 'kadept')
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>RSA Digital Signature:</strong> User ini memiliki kunci RSA untuk digital signature.
                                    
                                    @if ($user->public_key)
                                        <a href="{{ route('users.download-private-key', $user->id_user) }}" 
                                           class="btn btn-sm btn-success ml-2">
                                            <i class="fas fa-download"></i> Download Private Key
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="col-md-12">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>RSA Digital Signature:</strong> User dengan role {{ $user->role }} tidak memiliki kunci RSA.
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>

                    <button type="submit"
                            class="btn btn-warning">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endforeach
