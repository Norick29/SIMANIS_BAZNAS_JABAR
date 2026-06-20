@extends('layouts.public')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 85vh;">
    <div class="card shadow-sm border-0 w-100" style="max-width: 450px; border-radius: 12px;">
        
        {{-- HEADER FORM KHAS BAZNAS --}}
        <div class="card-header bg-white text-center pt-4 pb-2 border-0">
            <div class="mb-2" style="max-width: 90px; margin: 0 auto;">
                <img src="{{ asset('image/logo-baznas.png') }}" class="img-fluid" alt="Logo BAZNAS">
            </div>
            <h5 class="font-weight-bold text-dark mb-0" style="letter-spacing: 0.5px;">KONFIRMASI PASSWORD</h5>
            <small class="text-muted">SIM ASET BAZNAS Jabar</small>
        </div>

        <div class="card-body px-4 py-3">
            
            {{-- DESKRIPSI INFORMASI --}}
            <div class="alert alert-warning small text-justify mb-4" style="line-height: 1.5; border-radius: 8px; color: #856404; background-color: #fff3cd; border-color: #ffeeba;">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Ini adalah area aman aplikasi. Silakan masukkan kembali password Anda untuk mengonfirmasi identitas sebelum melanjutkan ke halaman sensitif.
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
            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                {{-- PASSWORD WITH SHOW/HIDE FEATURE --}}
                <div class="form-group mb-4">
                    <label recursive for="password" class="font-weight-bold text-xs text-muted text-uppercase mb-1" style="font-size: 11px;">
                        <i class="fas fa-lock mr-1"></i> Masukkan Password Anda
                    </label>
                    
                    {{-- INPUT GROUP UNTUK TOMBOL MATA --}}
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

                {{-- ACTION BUTTONS --}}
                <button type="submit" class="btn btn-success btn-block font-weight-bold shadow-sm py-2 mb-2" style="background-color: #2e7d32; border-color: #2e7d32;">
                    <i class="fas fa-check-circle mr-1"></i> Konfirmasi Password
                </button>

                <a href="{{ url()->previous() }}" class="btn btn-light btn-block btn-sm text-muted font-weight-bold">
                    <i class="fas fa-arrow-left mr-1"></i> Batalkan & Kembali
                </a>
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
</script>
@endsection