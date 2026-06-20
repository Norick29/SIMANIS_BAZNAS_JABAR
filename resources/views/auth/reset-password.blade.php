@extends('layouts.public')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 85vh;">
    <div class="card shadow-sm border-0 w-100" style="max-width: 450px; border-radius: 12px;">
        
        {{-- HEADER FORM KHAS BAZNAS --}}
        <div class="card-header bg-white text-center pt-4 pb-2 border-0">
            <div class="mb-2" style="max-width: 90px; margin: 0 auto;">
                <img src="{{ asset('image/logo-baznas.png') }}" class="img-fluid" alt="Logo BAZNAS">
            </div>
            <h5 class="font-weight-bold text-dark mb-0" style="letter-spacing: 0.5px;">PERBARUI PASSWORD</h5>
            <small class="text-muted">SIM ASET BAZNAS Jabar</small>
        </div>

        <div class="card-body px-4 py-3">
            
            {{-- DESKRIPSI INFORMASI --}}
            <div class="alert alert-success small text-justify mb-4" style="line-height: 1.4; border-radius: 8px;">
                <i class="fas fa-shield-alt mr-1"></i> Tautan verifikasi berhasil dikenali. Silakan masukkan alamat email Anda kembali dan buat kata sandi baru yang kuat untuk akun Anda.
            </div>

            {{-- ERROR HANDLING DARI VALIDASI BREEZE --}}
            @if ($errors->any())
                <div class="alert alert-danger small mb-3" role="alert">
                    <ul class="mb-0 pl-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- FORM SUBMIT --}}
            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                {{-- Password Reset Token (Hidden Input bawaan Breeze - KRUSIAL!) --}}
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                {{-- EMAIL ADDRESS --}}
                <div class="form-group mb-3">
                    <label for="email" class="font-weight-bold text-xs text-muted text-uppercase" style="font-size: 11px;">
                        <i class="fas fa-envelope mr-1"></i> Konfirmasi Alamat Email
                    </label>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email', $request->email) }}" 
                           required 
                           autofocus 
                           autocomplete="username">
                </div>

                {{-- PASSWORD BARU WITH SHOW/HIDE --}}
                <div class="form-group mb-3">
                    <label for="password" class="font-weight-bold text-xs text-muted text-uppercase mb-1" style="font-size: 11px;">
                        <i class="fas fa-key mr-1"></i> Password Baru
                    </label>
                    <div class="input-group">
                        <input id="password" 
                               type="password" 
                               name="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               placeholder="Minimal 8 karakter"
                               required 
                               autocomplete="new-password"
                               style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary toggle-password" 
                                    type="button" 
                                    data-target="password"
                                    style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-color: #ced4da;">
                                <i class="fas fa-eye eye-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- KONFIRMASI PASSWORD BARU WITH SHOW/HIDE --}}
                <div class="form-group mb-4">
                    <label for="password_confirmation" class="font-weight-bold text-xs text-muted text-uppercase mb-1" style="font-size: 11px;">
                        <i class="fas fa-check-double mr-1"></i> Ulangi Password Baru
                    </label>
                    <div class="input-group">
                        <input id="password_confirmation" 
                               type="password" 
                               name="password_confirmation" 
                               class="form-control @error('password_confirmation') is-invalid @enderror" 
                               placeholder="Masukkan kembali password"
                               required 
                               autocomplete="new-password"
                               style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary toggle-password" 
                                    type="button" 
                                    data-target="password_confirmation"
                                    style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-color: #ced4da;">
                                <i class="fas fa-eye eye-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ACTION BUTTONS --}}
                <button type="submit" class="btn btn-success btn-block font-weight-bold shadow-sm py-2 mb-2" style="background-color: #2e7d32; border-color: #2e7d32;">
                    <i class="fas fa-save mr-1"></i> Simpan Password Baru
                </button>
            </form>

        </div>

        {{-- FOOTER CARD --}}
        <div class="card-footer bg-light text-center py-3 border-0" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
            <small class="text-muted" style="font-size: 10px;">
                &copy; {{ date('Y') }} BAZNAS Jabar. Automated Logistical Control.
            </small>
        </div>

    </div>
</div>

{{-- JAVASCRIPT LOGIC GANDA UNTUK SHOW/HIDE PASSWORD --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Ambil semua tombol yang memiliki class .toggle-password
    const toggleButtons = document.querySelectorAll('.toggle-password');

    toggleButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Ambil id target input dari atribut data-target
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const eyeIcon = this.querySelector('.eye-icon');

            const isPassword = passwordInput.getAttribute('type') === 'password';
            
            if (isPassword) {
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
@endsection