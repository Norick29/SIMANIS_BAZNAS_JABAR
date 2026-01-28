@foreach ($users as $user)
<div class="modal fade"
     id="editUserModal{{ $user->id_user }}"
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

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text"
                                       name="name"
                                       class="form-control"
                                       value="{{ $user->name }}"
                                       required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email"
                                       name="email"
                                       class="form-control"
                                       value="{{ $user->email }}"
                                       required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone Number</label>
                                <input type="text"
                                       name="no_hp"
                                       class="form-control"
                                       value="{{ $user->no_hp }}"
                                       required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Role</label>
                                <select name="role" class="form-control" required>
                                    <option value="super admin" {{ $user->role == 'super admin' ? 'selected' : '' }}>Super Admin</option>
                                    <option value="kadiv" {{ $user->role == 'kadiv' ? 'selected' : '' }}>Kepala Divisi</option>
                                    <option value="kadept" {{ $user->role == 'kadept' ? 'selected' : '' }}>Kepala Departement</option>
                                    <option value="staf" {{ $user->role == 'staf' ? 'selected' : '' }}>Staf</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <small class="text-muted">
                                * Password tidak dapat diubah di halaman ini.
                            </small>
                        </div>

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
