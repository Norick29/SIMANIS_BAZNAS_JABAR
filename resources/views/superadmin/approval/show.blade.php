@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- TAMPILKAN ERROR / SUCCESS ALERT --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800 mb-0">
            <i class="fas fa-clipboard-list"></i> Detail Validasi Berkas Berdasarkan Sistem
        </h1>
        <a href="{{ route('approvals.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- CARD INFO APPROVAL UTAMA --}}
    <div class="card shadow mb-4">
        <div class="card-header bg-info text-white py-3">
            <h5 class="m-0 font-weight-bold">
                <i class="fas fa-file-alt"></i> Manifes Informasi Dokumen Pengajuan
            </h5>
        </div>
        {{-- ⚡ PERBAIKAN: Pisahkan pembacaan properti berdasarkan model aslinya secara akurat --}}
<div class="card-body text-dark">
    <div class="row">
        <div class="col-md-6">
            <p>
                <strong>Kode Master Aset:</strong><br>
                <span class="badge badge-info font-weight-bold" style="font-size: 13px;">
                   @if($approval->approval_type === \App\Models\PindahAset::class || $approval->id_pindah)
                        {{ $approval->pindahAset->asset->kode_aset ?? '-' }}
                    @elseif($approval->approval_type === \App\Models\PenyusutanAset::class || $approval->approval_type === \App\Models\PenghapusanAset::class)
                        {{ $approval->reference->asset->kode_aset ?? '-' }}
                    @endif
                </span>
            </p>
            <p>
                <strong>Nama Barang Inventaris:</strong><br>
                <span class="font-weight-bold">
                    @if($approval->approval_type === \App\Models\PindahAset::class || $approval->id_pindah)
                        {{ $approval->pindahAset->asset->barang->nama_barang ?? '-' }}
                    @elseif($approval->approval_type === \App\Models\PenyusutanAset::class || $approval->approval_type === \App\Models\PenghapusanAset::class)
                        {{ $approval->reference->asset->barang->nama_barang ?? '-' }}
                    @endif
                </span>
            </p>
            <p>
                <strong>Diajukan Oleh (Staf Logistik):</strong><br>
                {{ $approval->requestedBy->name ?? '-' }} (<span class="badge badge-secondary">{{ $approval->requestedBy->role ?? '-' }}</span>)
            </p>
            <p>
                <strong>Waktu Masuk Antrean Sistem:</strong><br>
                {{ $approval->created_at->format('d F Y, H:i') }} WIB
            </p>
        </div>
        <div class="col-md-6">
            <p>
                <strong>Status Keputusan Saat Ini:</strong><br>
                <span class="badge badge-warning font-weight-bold text-uppercase p-1">{{ $approval->status }}</span>
            </p>
            <p>
                <strong>Tingkat Otoritas (Stage Level):</strong><br>
                @if ($approval->stage === 1)
                    <span class="badge badge-warning font-weight-bold"><i class="fas fa-user-check"></i> Stage 1 - Kepala Departemen (Kadept)</span>
                @else
                    <span class="badge badge-info font-weight-bold"><i class="fas fa-user-shield"></i> Stage 2 - Kepala Divisi (Kadiv)</span>
                @endif
            </p>
        </div>
    </div>

    <hr class="my-4">

    {{-- ⚡ AREA RENDER SUB-BERKAS DETAIL (KODE BAWAAN YANG DI BAWAH INI JUGA HARUS DISESUAIKAN) ⚡ --}}
    @if($approval->approval_type === \App\Models\PindahAset::class || $approval->id_pindah != null)
        
        {{-- 📑 JALUR TAMPILAN MUTASI LOKASI ASET --}}
        <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-exchange-alt"></i> Detail Log Perubahan Titik Penempatan Aset</h6>
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted small mb-2 font-weight-bold text-uppercase">DARI (Kondisi Lama)</h6>
                <table class="table table-sm table-bordered">
                    <tr><th class="bg-light" width="40%">Lokasi Lama</th><td>{{ $approval->pindahAset->asset->lokasi->nama_lokasi ?? '-' }}</td></tr>
                    <tr><th class="bg-light">PJ Amil</th><td>{{ $approval->pindahAset->amilSebelum->nama ?? '-' }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted small mb-2 font-weight-bold text-uppercase text-success">KE (Rencana Usulan Baru)</h6>
                <table class="table table-sm table-bordered">
                    <tr><th class="bg-light" width="40%">Lokasi Baru</th><td class="table-success font-weight-bold">{{ $approval->pindahAset->lokasiBaru->nama_lokasi ?? '-' }}</td></tr>
                    <tr><th class="bg-light">PJ Amil Baru</th><td class="table-success font-weight-bold">{{ $approval->pindahAset->amilSesudah->nama ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-12">
                <h6 class="font-weight-bold mt-2">Alasan Pemindahan Mutasi:</h6>
                <div class="alert alert-light border text-justify">{{ $approval->pindahAset->alasan }}</div>
            </div>
        </div>

            @elseif(

                    $approval->approval_type === \App\Models\PenyusutanAset::class

                    ||

                    $approval->approval_type === \App\Models\PenghapusanAset::class

                )

                @php

                $isPeremajaan = Str::contains(
                    strtoupper($approval->note ?? ''),
                    'PEREMAJAAN'
                );

                $isPenghapusan =
                    $approval->approval_type === \App\Models\PenghapusanAset::class;

                @endphp
                
                <h6 class="font-weight-bold {{ $isPeremajaan ? 'text-warning' : 'text-danger' }} mb-3">
                    <i class="fas {{ $isPeremajaan ? 'fa-sync-alt' : 'fa-dumpster' }}"></i> 
                    Detail Dokumen Usulan {{ $isPenghapusan ? 'Penghapusan Aset' : 'Peremajaan Aset' }}
                </h6>
                
                <table class="table table-bordered">
                    <tr class="thead-light">
                        <th width="35%" class="bg-light">Jenis Keputusan Strategis</th>
                        <td class="font-weight-bold {{ $isPeremajaan ? 'text-warning' : 'text-danger' }}">
                            {{ $isPeremajaan ? '🔄 REVISI PEREMAJAAN (Perpanjang Masa Pakai 5 Tahun)' : '❌ KEPUTUSAN PENGHAPUSAN (Pemusnahan Berkas Fisik Aset)' }}
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Jenis Pengajuan</th>
                        <td>
                            <span class="badge {{ $isPenghapusan ? 'badge-danger' : 'badge-warning' }} font-weight-bold">
                                {{ $isPenghapusan ? 'Penghapusan Aset' : 'Peremajaan Aset' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">{{ $isPenghapusan ? 'Nilai Buku Aset Saat Diajukan' : 'Nilai Buku Investasi Sisa' }}</th>
                        <td class="font-weight-bold text-dark">
                            @if($isPenghapusan)
                                Rp {{ number_format($approval->reference->asset->total_harga ?? 0, 0, ',', '.') }}
                            @else
                                Rp {{ number_format($approval->reference->nilai_setelah_penyusutan ?? 0, 0, ',', '.') }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Tahun Perolehan Fisik</th>
                        <td>Tahun {{ $approval->reference->asset->tahun ?? '-' }} (Umur Aktif Berjalan: {{ (int)date('Y') - (int)$approval->reference->asset->tahun }} Tahun)</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Efek Jika Disetujui</th>
                        <td>
                            {{ $isPenghapusan
                                ? 'Aset akan dinonaktifkan dan masuk arsip riwayat penghapusan.'
                                : 'Nilai buku aset diperbarui dan kondisi aset dikembalikan menjadi baik.'
                            }}
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Alasan / Nota Penjelasan Staf</th>
                        <td class="text-justify bg-light font-italic text-danger font-weight-bold">
                            @if($isPenghapusan)

                                {{ $approval->reference->alasan }}

                                @else

                                {{ $approval->note }}

                            @endif
                        </td>
                    </tr>
                </table>

            @endif
        </div>
    </div>

    {{-- CARD APPROVAL ACTION (Password & TTD Digital RSA Tetap Utuh) --}}
    <div class="card shadow">
        <div class="card-header bg-warning text-dark py-3">
            <h5 class="m-0 font-weight-bold">
                <i class="fas fa-pen-alt"></i> Otorisasi Penandatanganan Dokumen Elektronik (Digital Signature)
            </h5>
        </div>
        <div class="card-body">
            <p class="text-muted small">
                <i class="fas fa-info-circle"></i> Silakan review rekam data di atas dengan teliti. Untuk memberikan validasi hukum berkas, masukkan kata sandi keamanan akun Anda sebagai kunci enkripsi tanda tangan digital RSA.
            </p>
            <ul class="nav nav-tabs mb-3" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold text-success" id="approve-tab" data-toggle="tab" href="#approve">
                        <i class="fas fa-check-circle"></i> Setujui Berkas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold text-danger" id="reject-tab" data-toggle="tab" href="#reject">
                        <i class="fas fa-times-circle"></i> Tolak Permintaan
                    </a>
                </li>
            </ul>

            <div class="tab-content text-dark">
                {{-- TAB APPROVE --}}
                <div class="tab-pane fade show active" id="approve">
                    <form action="{{ route('approvals.approve', $approval->id_approval) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="font-weight-bold">Password Konfirmasi Akun Pimpinan</label>
                            <input type="password" name="password" class="form-control col-md-6" placeholder="Masukkan password Anda..." required>
                            <small class="text-muted d-block mt-1">
                                *Tindakan ini akan mengunci status dokumen secara permanen menggunakan tanda tangan digital enkripsi RSA-SHA256.
                            </small>
                        </div>
                        <button type="submit" class="btn btn-success font-weight-bold px-4 shadow-sm">
                            <i class="fas fa-check-circle mr-1"></i> Sahkan & Tandatangani Secara Digital
                        </button>
                    </form>
                </div>

                {{-- TAB REJECT --}}
                <div class="tab-pane fade" id="reject">
                    <form action="{{ route('approvals.reject', $approval->id_approval) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="font-weight-bold text-danger">Alasan Penolakan Audit Berkas</label>
                            <textarea name="alasan" class="form-control" rows="3" placeholder="Jelaskan alasan mengapa pengajuan ditolak..." required></textarea>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Password Verifikasi Otoritas</label>
                            <input type="password" name="password" class="form-control col-md-6" placeholder="Masukkan password Anda..." required>
                        </div>
                        <div class="alert alert-warning p-2 small border-left-warning" style="border-left-width: 4px;">
                            <i class="fas fa-exclamation-triangle mr-1 text-danger"></i> <strong>Pernyataan Batasan Tindakan:</strong><br>
                            Mengklik tombol di bawah akan langsung membatalkan seluruh rentetan berkas usulan staf. Status akan terkunci ke kategori <strong>Ditolak</strong> dan tidak dapat diteruskan ke tingkatan selanjutnya.
                        </div>
                        <button type="submit" class="btn btn-danger font-weight-bold px-4 shadow-sm">
                            <i class="fas fa-times-circle mr-1"></i> Tolak & TTD Digital Pembatalan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
