@extends('layouts.app')

@section('title', 'Detail Berita Acara Penghapusan Aset')

@section('content')
<div class="container-fluid">
    {{-- Tombol Kembali --}}
    <div class="mb-3">
        <a href="{{ route('penghapusan.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
        
        @if($penghapusan->status == 'disetujui')
            <a href="{{ route('penghapusan.cetak-ba', $penghapusan->id_penghapusan) }}" class="btn btn-primary btn-sm shadow-sm float-right" target="_blank">
                <i class="fas fa-file-pdf"></i> Cetak PDF Berita Acara
            </a>
        @endif
    </div>

    <div class="card shadow mb-4">
        <div class="card-body p-5">
            <div class="text-center mb-5">
                <h3 class="font-weight-bold text-gray-800">BERITA ACARA</h3>
                <h5 class="text-gray-600">PENGHAPUSAN DAN PEMUSNAHAN ASET</h5>
                <hr style="border-top: 2px dashed #e3e6f0;">
            </div>

            <p class="text-gray-700">Pada hari ini, sistem mencatat validasi akhir atas tindakan penghapusan dan penonaktifan aset dengan rincian data sebagai berikut:</p>

            <div class="table-responsive">
                <table class="table table-bordered table-striped text-gray-800">
                    <tr>
                        <th width="30%" class="bg-light font-weight-bold">Kode Aset</th>
                        <td class="font-weight-bold text-danger">
                            <span class="badge badge-danger-light p-1 px-2 border border-danger">
                                {{ $penghapusan->asset->kode_aset ?? '-' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light font-weight-bold">Nama Barang</th>
                        <td>{{ $penghapusan->asset->barang->nama_barang ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light font-weight-bold">Lokasi Terakhir</th>
                        <td>{{ $penghapusan->asset->lokasi->nama_lokasi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light font-weight-bold">Amil Penanggung Jawab</th>
                        <td>{{ $penghapusan->asset->amil->nama ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light font-weight-bold">Diajukan Oleh</th>
                        <td>{{ $penghapusan->requestedBy->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light font-weight-bold">Tanggal Eksekusi</th>
                        <td>{{ \Carbon\Carbon::parse($penghapusan->created_at)->locale('id')->isoFormat('D MMMM Y') }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light font-weight-bold">Status Log</th>
                        <td>
                            @if($penghapusan->status == 'disetujui')
                                <span class="badge badge-success px-3 py-1">
                                    <i class="fas fa-check-circle"></i> Selesai & Nonaktif
                                </span>
                            @elseif($penghapusan->status == 'menunggu')
                                <span class="badge badge-warning px-3 py-1">
                                    <i class="fas fa-clock"></i> Menunggu
                                </span>
                            @else
                                <span class="badge badge-danger px-3 py-1">
                                    <i class="fas fa-times-circle"></i> Ditolak
                                </span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            <h5 class="font-weight-bold mt-5 text-gray-800">
                <i class="fas fa-align-left text-secondary mr-2"></i> Alasan & Pertimbangan Penghapusan
            </h5>
            <div class="p-4 bg-light border rounded text-justify text-gray-700 shadow-sm" style="white-space: pre-line; line-height: 1.6; border-left: 5px solid #4e73df !important;">
                {{ $penghapusan->alasan }}
            </div>

            <div class="mt-5 text-muted small text-center border-top pt-3">
                Dokumen digital ini divalidasi penuh oleh Sistem Informasi Manajemen Aset berdasarkan persetujuan Kepala Divisi[cite: 255]. 
                <br>Gunakan fitur ekspor PDF di pojok kanan atas untuk mencetak fisik lembar Berita Acara.
            </div>
        </div>
    </div>
</div>
@endsection