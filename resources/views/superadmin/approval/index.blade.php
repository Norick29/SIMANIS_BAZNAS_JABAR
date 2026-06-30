@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-clipboard-check"></i> Manajemen Validasi Dokumen (Approval)
        </h1>
    </div>

    {{-- STATUS ALERT --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-list"></i> Daftar Permintaan Validasi Menunggu Persetujuan
            </h6>
        </div>

        <div class="card-body">
            @if ($approvals->isEmpty())
                <div class="alert alert-info text-center mb-0">
                    <i class="fas fa-inbox"></i> Tidak ada berkas permintaan approval yang sedang menunggu.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="thead-light text-center">
                            <tr>
                                <th>Jenis Pengajuan</th>
                                <th>Kode Aset</th>
                                <th>Nama Barang</th>
                                <th>Manifes Informasi Berkas</th>
                                <th>Peminta</th>
                                <th>Tingkat Stage</th>
                                <th>Tgl Masuk</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($approvals as $approval)
                                <tr class="text-center align-middle">
                                    
                                    {{-- 1. IDENTIFIKASI JENIS PENGAJUAN (POLYMORPHIC RENDER) --}}
                                    <td>

                                        @if($approval->approval_type === \App\Models\PindahAset::class || $approval->id_pindah)

                                            <span class="badge badge-primary">
                                                <i class="fas fa-exchange-alt"></i>
                                                Mutasi Lokasi
                                            </span>

                                        @elseif($approval->approval_type === \App\Models\PenyusutanAset::class)

                                            @if(Str::contains($approval->note,'PEREMAJAAN'))

                                                <span class="badge badge-warning">
                                                    <i class="fas fa-sync-alt"></i>
                                                    Peremajaan
                                                </span>

                                            @else

                                                <span class="badge badge-info">
                                                    Penyusutan
                                                </span>

                                            @endif

                                        @elseif($approval->approval_type === \App\Models\PenghapusanAset::class)

                                            <span class="badge badge-danger">
                                                <i class="fas fa-trash"></i>
                                                Penghapusan
                                            </span>

                                        @else

                                            <span class="badge badge-secondary">
                                                Umum
                                            </span>

                                        @endif

                                    </td>

                                    {{-- 2. KODE ASET ADAPTIF --}}
                                    <td class="font-weight-bold text-success">

                                        @if($approval->approval_type === \App\Models\PindahAset::class || $approval->id_pindah)

                                            {{ $approval->pindahAset->asset->kode_aset ?? '-' }}

                                        @elseif(
                                            $approval->approval_type === \App\Models\PenyusutanAset::class
                                            ||
                                            $approval->approval_type === \App\Models\PenghapusanAset::class
                                        )

                                            {{ $approval->reference->asset->kode_aset ?? '-' }}

                                        @endif

                                    </td>

                                    {{-- 3. NAMA BARANG ADAPTIF --}}
                                    <td>

                                        @if($approval->approval_type === \App\Models\PindahAset::class || $approval->id_pindah)

                                            {{ $approval->pindahAset->asset->barang->nama_barang ?? '-' }}

                                        @elseif(
                                            $approval->approval_type === \App\Models\PenyusutanAset::class
                                            ||
                                            $approval->approval_type === \App\Models\PenghapusanAset::class
                                        )

                                            {{ $approval->reference->asset->barang->nama_barang ?? '-' }}

                                        @endif
                                    </td>

                                    {{-- 4. MANIFES DETAIL DOKUMEN ADAPTIF --}}
                                    <td class="text-left">
                                        @if($approval->approval_type === \App\Models\PindahAset::class || $approval->id_pindah != null)
                                            <small class="d-block">
                                                <strong>Ke Lokasi:</strong> {{ $approval->pindahAset->lokasiBaru->nama_lokasi ?? '-' }}<br>
                                                <strong>PJ Baru:</strong> {{ $approval->pindahAset->amilSesudah->nama ?? '-' }}
                                            </small>
                                        @elseif($approval->approval_type === \App\Models\PenyusutanAset::class)
                                            <small class="d-block text-justify text-danger font-weight-bold">
                                                {{ $approval->note ?? 'Usulan rekayasa kondisi fisik masa pakai.' }}
                                            </small>
                                        @elseif($approval->approval_type === \App\Models\PenghapusanAset::class)

                                            <small>

                                            <strong>Jenis :</strong>

                                            Penghapusan Permanen

                                            <br>

                                            <strong>Alasan :</strong>

                                            {{ $approval->reference->alasan }}

                                            </small>
                                        @endif
                                    </td>

                                    {{-- 5. PEMINTA --}}
                                    <td>{{ $approval->requestedBy->name ?? '-' }}</td>

                                    {{-- 6. STAGE BADGE --}}
                                    <td>
                                        @if ($approval->stage === 1)
                                            <span class="badge badge-warning font-weight-bold p-1"><i class="fas fa-user-check"></i> Stage 1 - Kadept</span>
                                        @else
                                            <span class="badge badge-info font-weight-bold p-1"><i class="fas fa-user-shield"></i> Stage 2 - Kadiv</span>
                                        @endif
                                    </td>

                                    {{-- 7. TANGGAL MASUK --}}
                                    <td><small>{{ $approval->created_at->format('d M Y, H:i') }} WIB</small></td>

                                    {{-- 8. LINK REVIEW TOMBOL AKSI --}}
                                    <td>
                                        @if(auth()->user()->role !== 'super admin')
                                            <a href="{{ route('approvals.show', $approval->id_approval) }}" class="btn btn-sm btn-success font-weight-bold shadow-sm">
                                                <i class="fas fa-search-plus"></i> Periksa Berkas
                                            </a>
                                        @else
                                            <span class="badge badge-secondary"><i class="fas fa-lock"></i> Hanya Pantauan</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection