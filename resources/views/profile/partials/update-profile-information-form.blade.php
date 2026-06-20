<section>
    <header class="mb-3">
        <h5 class="text-dark font-weight-bold mb-1">Data Diri</h5>
        <p class="text-muted small">Perbarui data nama dan alamat email resmi akun Anda di sistem.</p>
    </header>

    {{-- Form Verifikasi Email (Kosongkan/Sembunyikan karena bawaan Breeze) --}}
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    {{-- FORM UTAMA --}}
    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch') {{-- JANGAN DIUBAH! Ini token patch asli bawaan controller --}}

        <div class="form-group mb-3">
            <label for="name" class="font-weight-bold small text-muted text-uppercase">Nama Lengkap Amil</label>
            <input id="name" 
                   name="name" 
                   type="text" 
                   class="form-control @error('name') is-invalid @enderror" 
                   value="{{ old('name', $user->name) }}" 
                   required 
                   autofocus 
                   autocomplete="name">
            @error('name')
                <div class="invalid-feedback font-weight-bold">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="email" class="font-weight-bold small text-muted text-uppercase">Alamat Email Sistem</label>
            <input id="email" 
                   name="email" 
                   type="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   value="{{ old('email', $user->email) }}" 
                   required 
                   autocomplete="username">
            @error('email')
                <div class="invalid-feedback font-weight-bold">{{ $message }}</div>
            @enderror
        </div>

        <hr>

        <div class="text-right">
            <button type="submit" class="btn btn-success font-weight-bold shadow-sm px-4" style="background-color: #2e7d32; border-color: #2e7d32;">
                <i class="fas fa-save mr-1"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</section>