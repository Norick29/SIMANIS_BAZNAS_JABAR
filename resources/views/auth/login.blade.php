@extends('layouts.public')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 85vh;">
    <div class="card shadow-sm border-0 w-100" style="max-width: 450px; border-radius: 12px;">
        
        {{-- HEADER FORM (KHAS BAZNAS) --}}
        <div class="card-header bg-white text-center pt-4 pb-2 border-0">
            <div class="mb-2" style="max-width: 90px; margin: 0 auto;">
                <img src="{{ asset('image/logo-baznas.png') }}" class="img-fluid" alt="Logo BAZNAS">
            </div>
            <h5 class="font-weight-bold text-dark mb-0" style="letter-spacing: 0.5px;">SIMASET BAZNAS</h5>
            <small class="text-muted">Provinsi Jawa Barat</small>
        </div>

        <div class="card-body px-4 py-3">
            
            {{-- ERROR HANDLING / SESSION STATUS BAWAAN BREEZE --}}
            @if (session('status'))
                <div class="alert alert-success small mb-3" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Menampilkan Error Global/Validasi dari Breeze jika Login Gagal --}}
            @if ($errors->any())
                <div class="alert alert-danger small mb-3" role="alert">
                    <ul class="mb-0 pl-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- FORM LOGIN --}}
            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- EMAIL ADDRESS --}}
                <div class="form-group mb-3">
                    <label for="email" class="font-weight-bold text-xs text-muted text-uppercase" style="font-size: 11px;">
                        <i class="fas fa-envelope mr-1"></i> Alamat Email
                    </label>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email') }}" 
                           placeholder="contoh: amil@baznas.go.id"
                           required 
                           autofocus 
                           autocomplete="username">
                </div>

                {{-- PASSWORD WITH SHOW/HIDE FEATURE --}}
                <div class="form-group mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="font-weight-bold text-xs text-muted text-uppercase mb-0" style="font-size: 11px;">
                            <i class="fas fa-lock mr-1"></i> Password
                        </label>
                        @if (Route::has('password.request'))
                            <a class="text-xs text-success font-weight-bold" style="font-size: 11px; text-decoration: none;" href="{{ route('password.request') }}">
                                Lupa Password?
                            </a>
                        @endif
                    </div>
                    
                    {{-- BOOTSTRAP INPUT GROUP UNTUK MENYELIPKAN TOMBOL MATA --}}
                    <div class="input-group">
                        <input id="password" 
                               type="password" 
                               name="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               placeholder="••••••••"
                               required 
                               autocomplete="current-password"
                               style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" 
                                    type="button" 
                                    id="togglePassword"
                                    style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-color: #ced4da;">
                                <i class="fas fa-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- REMEMBER ME --}}
                <div class="form-group custom-control custom-checkbox my-3">
                    <input type="checkbox" class="custom-control-input" id="remember_me" name="remember">
                    <label class="custom-control-label text-muted small" for="remember_me">
                        Ingat Saya di Perangkat Ini
                    </label>
                </div>

                {{-- BUTTON SUBMIT --}}
                <button type="submit" class="btn btn-success btn-block font-weight-bold shadow-sm mt-4 py-2" style="background-color: #2e7d32; border-color: #2e7d32;">
                    <i class="fas fa-sign-in-alt mr-1"></i> Masuk ke Sistem
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

{{-- JAVASCRIPT LOGIC UNTUK SHOW/HIDE PASSWORD --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('password');
    const togglePasswordButton = document.getElementById('togglePassword');
    const eyeIcon = document.getElementById('eyeIcon');

    togglePasswordButton.addEventListener('click', function () {
        // Cek tipe input saat ini
        const isPassword = passwordInput.getAttribute('type') === 'password';
        
        if (isPassword) {
            // Ubah tipe ke text (Tampilkan password)
            passwordInput.setAttribute('type', 'text');
            // Ganti ikon ke mata dicoret (eye-slash)
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            // Kembalikan ke password (Sembunyikan password)
            passwordInput.setAttribute('type', 'password');
            // Ganti ikon balik ke mata biasa (eye)
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    });
});
</script>
@endsection