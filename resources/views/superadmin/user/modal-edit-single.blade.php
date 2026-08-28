@foreach ($users as $u)
<div class="modal fade"
     id="editUserModal{{ $u->id_user }}"
     tabindex="-1"
     role="dialog"
     aria-labelledby="editUserLabel{{ $u->id_user }}"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg">

            {{-- HEADER --}}
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editUserLabel{{ $u->id_user }}">
                    <i class="fas fa-user-edit mr-2"></i> Edit User: {{ $u->name }}
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            {{-- FORM --}}
            <form action="{{ route('users.update', $u->id_user) }}" method="POST">
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
                                       value="{{ $u->name }}"
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
                                       value="{{ $u->email }}"
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
                                       value="{{ $u->no_hp }}"
                                       required>
                            </div>
                        </div>

                        {{-- ROLE --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Role</strong></label>
                                <select name="role" class="form-control" required>
                                    <option value="super admin" {{ $u->role == 'super admin' ? 'selected' : '' }}>Super Admin</option>
                                    <option value="kadiv" {{ $u->role == 'kadiv' ? 'selected' : '' }}>Kepala Divisi</option>
                                    <option value="kadept" {{ $u->role == 'kadept' ? 'selected' : '' }}>Kepala Departement</option>
                                    <option value="staf" {{ $u->role == 'staf' ? 'selected' : '' }}>Staf</option>
                                </select>
                            </div>
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
