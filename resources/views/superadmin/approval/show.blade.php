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
            <i class="fas fa-clipboard-list"></i> Detail Approval
        </h1>
        <a href="{{ route('approvals.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- CARD INFO APPROVAL --}}
    <div class="card shadow mb-4">
        <div class="card-header bg-info text-white py-3">
            <h5 class="m-0">Informasi Permintaan</h5>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p>
                        <strong>Kode Aset:</strong><br>
                        <span class="badge badge-info">{{ $approval->pindahAset->asset->kode_aset }}</span>
                    </p>
                    <p>
                        <strong>Barang:</strong><br>
                        {{ $approval->pindahAset->asset->barang->nama_barang }}
                    </p>
                    <p>
                        <strong>Diminta oleh:</strong><br>
                        {{ $approval->requestedBy->name ?? '-' }} ({{ $approval->requestedBy->role ?? '-' }})
                    </p>
                    <p>
                        <strong>Tanggal Permintaan:</strong><br>
                        {{ $approval->created_at->format('d M Y H:i') }}
                    </p>
                </div>

                <div class="col-md-6">
                    <p>
                        <strong>Status Approval:</strong><br>
                        <span class="badge badge-warning">{{ ucfirst($approval->status) }}</span>
                    </p>
                    <p>
                        <strong>Stage:</strong><br>
                        @if ($approval->stage === 1)
                            <span class="badge badge-warning">Stage 1 - Kepala Departemen</span>
                        @else
                            <span class="badge badge-info">Stage 2 - Kepala Divisi</span>
                        @endif
                    </p>
                </div>
            </div>

            <hr>

            <h6 class="font-weight-bold">Detail Perpindahan Aset</h6>
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted small mb-2">DARI (Sebelum)</h6>
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th>Lokasi</th>
                            <td>{{ $approval->pindahAset->asset->lokasi->nama_lokasi }}</td>
                        </tr>
                        <tr>
                            <th>Penanggung Jawab</th>
                            <td>{{ $approval->pindahAset->amilSebelum->nama }}</td>
                        </tr>
                        <tr>
                            <th>NIA</th>
                            <td>{{ $approval->pindahAset->amilSebelum->nia }}</td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted small mb-2">KE (Sesudah)</h6>
                    <table class="table table-sm table-bordered">
                        <tr>
                            <th>Lokasi</th>
                            <td>{{ $approval->pindahAset->lokasiBaru->nama_lokasi }}</td>
                        </tr>
                        <tr>
                            <th>Penanggung Jawab</th>
                            <td>{{ $approval->pindahAset->amilSesudah->nama }}</td>
                        </tr>
                        <tr>
                            <th>NIA</th>
                            <td>{{ $approval->pindahAset->amilSesudah->nia }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <h6 class="font-weight-bold mt-3">Alasan Perpindahan</h6>
                    <div class="alert alert-light border">
                        {{ $approval->pindahAset->alasan }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CARD APPROVAL ACTION --}}
    <div class="card shadow">
        <div class="card-header bg-warning text-dark py-3">
            <h5 class="m-0">
                <i class="fas fa-pen-alt"></i> Keputusan Approval
            </h5>
        </div>

        <div class="card-body">
            <p class="text-muted">
                <i class="fas fa-info-circle"></i> 
                Silakan review informasi di atas. Untuk melakukan approval, Anda harus mengunggah private key RSA Anda dan memasukkan password.
            </p>

            <ul class="nav nav-tabs mb-3" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="approve-tab" data-toggle="tab" href="#approve">
                        <i class="fas fa-check text-success"></i> Setujui
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="reject-tab" data-toggle="tab" href="#reject">
                        <i class="fas fa-times text-danger"></i> Tolak
                    </a>
                </li>
            </ul>

            {{-- TAB APPROVE --}}
            <div class="tab-content">
                <div class="tab-pane fade show active" id="approve">
                    <form action="{{ route('approvals.approve', $approval->id_approval) }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label><strong>Password Konfirmasi</strong></label>

                            <input type="password"
                                name="password"
                                class="form-control"
                                required>

                            <small class="text-muted">
                                Masukkan password akun Anda untuk digital signature
                            </small>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-check-circle"></i>
                            Setujui & Tandatangani
                        </button>
                    </form>
                </div>

                {{-- TAB REJECT --}}
                <div class="tab-pane fade" id="reject">
                    <form action="{{ route('approvals.reject', $approval->id_approval) }}" 
                          method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label><strong>Alasan Penolakan</strong></label>
                            <textarea name="alasan" class="form-control" rows="4" 
                                      placeholder="Jelaskan alasan penolakan approval ini" required></textarea>
                        </div>

                        <div class="form-group">
                            <label><strong>Password</strong></label>
                            <input type="password" name="password" class="form-control" required>
                            <small class="form-text text-muted">
                                Masukkan password untuk verifikasi keamanan
                            </small>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Perhatian!</strong><br>
                            <small>
                                Dengan meng-klik tombol "Tolak", Anda akan menolak permintaan pindah aset ini dengan tanda tangan digital. 
                                Permintaan tidak dapat dilanjutkan ke stage berikutnya.
                            </small>
                        </div>

                        <button type="submit" class="btn btn-danger btn-lg">
                            <i class="fas fa-times-circle"></i> Tolak & Tandatangan Digital
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection