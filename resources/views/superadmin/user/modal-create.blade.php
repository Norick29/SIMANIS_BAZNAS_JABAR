<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="createUserLabel">Add New User</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <form id="createUserForm" action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="modal-body">

                    {{-- Name --}}
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input 
                            type="text" 
                            name="name" 
                            class="form-control @error('name') is-invalid @enderror" 
                            placeholder="Enter full name"
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input 
                            type="email" 
                            name="email" 
                            class="form-control @error('email') is-invalid @enderror" 
                            placeholder="Enter email"
                            required
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group">
                            <input 
                                id="password" 
                                type="password" 
                                name="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                placeholder="Enter password"
                                required
                            >
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#password" aria-label="Toggle password visibility">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Phone Number --}}
                    <div class="form-group">
                        <label for="no_hp">Phone Number</label>
                        <input 
                            type="text" 
                            name="no_hp" 
                            class="form-control @error('no_hp') is-invalid @enderror" 
                            placeholder="Enter phone number"
                            required
                        >
                        @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select 
                            name="role" 
                            class="form-control @error('role') is-invalid @enderror"
                            required>
                            <option value="" disabled selected>-- Select Role --</option>
                            <option value="super admin">Super Admin</option>
                            <option value="kadiv">Kepala Divisi</option>
                            <option value="kadept">Kepala Departement</option>
                            <option value="staf">Staff</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Save User
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- Toggle password visibility --}}
<script>
document.addEventListener('click', function(e){
    const btn = e.target.closest('.toggle-password');
    if (!btn) return;
    const targetSelector = btn.getAttribute('data-target');
    const input = document.querySelector(targetSelector);
    const icon = btn.querySelector('i');
    if (!input) return;
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});

// reset form + UI helper
function resetCreateUserForm() {
    const form = document.getElementById('createUserForm');
    if (!form) return;

    // reset fields
    form.reset();

    // remove validation classes
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    // remove server-side invalid-feedback blocks (optional: keep if you want)
    form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());

    // reset password fields to masked and eye icons to default
    document.querySelectorAll('.toggle-password').forEach(btn => {
        const target = document.querySelector(btn.getAttribute('data-target'));
        if (target) target.type = 'password';
        const icon = btn.querySelector('i');
        if (icon) {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
}

// Bootstrap event (jQuery) if available
if (window.jQuery) {
    $('#createUserModal').on('hidden.bs.modal', resetCreateUserForm);
}

// fallback: reset when any data-dismiss button inside modal is clicked
document.querySelectorAll('#createUserModal [data-dismiss="modal"]').forEach(btn => {
    btn.addEventListener('click', resetCreateUserForm);
});
</script>
