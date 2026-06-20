<section>
    <header class="mb-3">
        <h5 class="text-dark font-weight-bold mb-1">Keamanan Akun</h5>
        <p class="text-muted small">Pastikan akun Anda menggunakan kata sandi yang panjang dan acak demi menjaga integritas data logistik aset.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put') {{-- JANGAN DIUBAH! Ini token put asli bawaan controller password --}}

        <div class="form-group mb-3">
            <label for="update_password_current_password" class="font-weight-bold small text-muted text-uppercase">Password Saat Ini</label>
            <input id="update_password_current_password" 
                   name="current_password" 
                   type="password" 
                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                   placeholder="Masukkan password sekarang"
                   autocomplete="current-password" 
                   required>
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback font-weight-bold">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="update_password_password" class="font-weight-bold small text-muted text-uppercase">Password Baru</label>
            <div class="input-group">
                <input id="update_password_password" 
                       name="password" 
                       type="password" 
                       class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                       placeholder="Minimal 8 karakter"
                       autocomplete="new-password" 
                       required
                       style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary toggle-profile-password" 
                            type="button" 
                            data-target="update_password_password" 
                            style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-color: #ced4da;">
                        <i class="fas fa-eye eye-icon"></i>
                    </button>
                </div>
                @error('password', 'updatePassword')
                    <div class="invalid-feedback font-weight-bold d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group mb-4">
            <label for="update_password_password_confirmation" class="font-weight-bold small text-muted text-uppercase">Konfirmasi Password Baru</label>
            <div class="input-group">
                <input id="update_password_password_confirmation" 
                       name="password_confirmation" 
                       type="password" 
                       class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" 
                       placeholder="Ulangi password baru"
                       autocomplete="new-password" 
                       required
                       style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary toggle-profile-password" 
                            type="button" 
                            data-target="update_password_password_confirmation" 
                            style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-color: #ced4da;">
                        <i class="fas fa-eye eye-icon"></i>
                    </button>
                </div>
                @error('password_confirmation', 'updatePassword')
                    <div class="invalid-feedback font-weight-bold d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <hr>

        <div class="text-right">
            <button type="submit" class="btn btn-dark font-weight-bold shadow-sm px-4">
                <i class="fas fa-key mr-1"></i> Perbarui Password
            </button>
        </div>
    </form>
</section>

{{-- JAVASCRIPT EYE TOGGLE KILAT KHUSUS HALAMAN PROFILE --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleButtons = document.querySelectorAll('.toggle-profile-password');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const eyeIcon = this.querySelector('.eye-icon');

            if (passwordInput.getAttribute('type') === 'password') {
                passwordInput.setAttribute('type', 'text');
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.setAttribute('type', 'password');
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
    });
});
</script>