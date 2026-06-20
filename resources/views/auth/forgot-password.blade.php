@extends('layouts.public')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 85vh;">
    <div class="card shadow-sm border-0 w-100" style="max-width: 450px; border-radius: 12px;">
        
        {{-- HEADER FORM KHAS BAZNAS --}}
        <div class="card-header bg-white text-center pt-4 pb-2 border-0">
            <div class="mb-2" style="max-width: 90px; margin: 0 auto;">
                <img src="{{ asset('image/logo-baznas.png') }}" class="img-fluid" alt="Logo BAZNAS">
            </div>
            <h5 class="font-weight-bold text-dark mb-0" style="letter-spacing: 0.5px;">RESET PASSWORD</h5>
            <small class="text-muted">SIMASET BAZNAS Jabar</small>
        </div>

        <div class="card-body px-4 py-3">
            
            {{-- DESKRIPSI INFORMASI --}}
            <div class="alert alert-info small text-justify mb-4" style="line-height: 1.5; border-radius: 8px;">
                <i class="fas fa-info-circle mr-1"></i>
                Lupa password Anda? Tidak masalah. Silakan masukkan alamat email yang terdaftar di sistem. Kami akan mengirimkan tautan (*link*) reset password untuk mengatur ulang kata sandi Anda.
            </div>

            {{-- SESSION STATUS (NOTIFIKASI EMAIL BERHASIL DIKIRIM) --}}
            @if (session('status'))
                <div class="alert alert-success small font-weight-bold mb-3" role="alert">
                    <i class="fas fa-check-circle mr-1"></i> {{ session('status') }}
                </div>
            @endif

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
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                {{-- EMAIL ADDRESS --}}
                <div class="form-group mb-4">
                    <label for="email" class="font-weight-bold text-xs text-muted text-uppercase" style="font-size: 11px;">
                        <i class="fas fa-envelope mr-1"></i> Masukkan Email Terdaftar
                    </label>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email') }}" 
                           placeholder="contoh: amil@baznas.go.id"
                           required 
                           autofocus>
                </div>

                {{-- ACTION BUTTONS --}}
                <button type="submit" class="btn btn-success btn-block font-weight-bold shadow-sm py-2 mb-2" style="background-color: #2e7d32; border-color: #2e7d32;">
                    <i class="fas fa-paper-plane mr-1"></i> Kirim Link Reset Password
                </button>

                <a href="{{ route('login') }}" class="btn btn-light btn-block btn-sm text-muted font-weight-bold">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Halaman Login
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
@endsection