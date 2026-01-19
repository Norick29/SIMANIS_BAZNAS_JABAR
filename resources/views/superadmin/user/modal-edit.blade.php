@foreach ($users as $user)
<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal{{ $user->id_user }}" tabindex="-1" role="dialog"
     aria-labelledby="editUserLabel{{ $user->id_user }}" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="editUserLabel{{ $user->id_user }}">Edit User</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <form action="{{ route('users.update', $user->id_user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">

                    {{-- Full Name --}}
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" 
                               name="name" 
                               value="{{ $user->name }}"
                               class="form-control" 
                               required>
                    </div>

                    {{-- Email --}}
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" 
                               name="email" 
                               value="{{ $user->email }}"
                               class="form-control"
                               required>
                    </div>

                    {{-- Phone Number --}}
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" 
                               name="no_hp" 
                               value="{{ $user->no_hp }}"
                               class="form-control"
                               required>
                    </div>

                    {{-- Role --}}
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control" required>
                            <option value="super admin" {{ $user->role === 'super admin' ? 'selected' : '' }}>Super Admin</option>
                            <option value="kadiv" {{ $user->role === 'kadiv' ? 'selected' : '' }}>Kepala Divisi</option>
                            <option value="kadept" {{ $user->role === 'kadept' ? 'selected' : '' }}>Kepala Departement</option>
                            <option value="staf" {{ $user->role === 'staf' ? 'selected' : '' }}>Staf</option>
                        </select>
                    </div>

                    <small class="text-muted">
                        * Password cannot be changed here.<br>
                        * Password changes can only be done from the user's Profile page.
                    </small>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Update User
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endforeach
