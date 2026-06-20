@extends('layouts.app') {{-- Sesuaikan nama layout utama dashboard SB Admin 2 kamu --}}

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-user-cog mr-2"></i>Pengaturan Akun</h1>
    </div>

    {{-- ALERT STATUS SUKSES GLOBAL --}}
    @if (session('status') === 'profile-updated')
        <div class="alert alert-success small font-weight-bold shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle mr-1"></i> Informasi profil Anda berhasil diperbarui!
        </div>
    @endif
    @if (session('status') === 'password-updated')
        <div class="alert alert-success small font-weight-bold shadow-sm mb-4" role="alert">
            <i class="fas fa-key mr-1"></i> Kata sandi akun berhasil diperbarui secara aman!
        </div>
    @endif

    <div class="row">
        {{-- KOLOM KIRI: UPDATE PROFILE INFORMATION --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow border-0 mb-4">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-id-card mr-1"></i> Informasi Profil Amil</h6>
                </div>
                <div class="card-body px-4 py-4">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: UPDATE PASSWORD --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow border-0 mb-4">
                <div class="card-header py-3 bg-dark text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-lock mr-1"></i> Perbarui Kata Sandi</h6>
                </div>
                <div class="card-body px-4 py-4">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>

</div>
@endsection