@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Tombol Kembali --}}
    <div class="mb-3">
        <a href="{{ route('hilang.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body p-5">
            <div class="text-center mb-5">
                <h3 class="font-weight-bold">BERITA ACARA</h3>
                <h5>KRONOLOGI KEHILANGAN ASET</h5>
                <hr>
            </div>

            <p>Pada hari ini, sistem mencatat pelaporan insiden kehilangan aset dengan detail informasi sebagai berikut:</p>

            <table class="table table-bordered">
                <tr>
                    <th width="30%" class="bg-light">Kode Aset</th>
                    <td class="font-weight-bold text-danger">{{ $hilang->asset->kode_aset }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Nama Barang</th>
                    <td>{{ $hilang->asset->barang->nama_barang ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Lokasi Terakhir</th>
                    <td>{{ $hilang->asset->lokasi->nama_lokasi ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Amil Penanggung Jawab</th>
                    <td>{{ $hilang->asset->amil->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Tanggal Kehilangan</th>
                    <td>{{ \Carbon\Carbon::parse($hilang->tanggal_hilang)->locale('id')->isoFormat('D MMMM Y') }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Dilaporkan Oleh</th>
                    <td>{{ $hilang->user->name ?? '-' }}</td>
                </tr>
            </table>

            <h5 class="font-weight-bold mt-4"><i class="fas fa-align-left text-secondary"></i> Catatan Kronologi Kejadian</h5>
            <div class="p-4 bg-light border rounded text-justify" style="white-space: pre-line; line-height: 1.6;">
                {{ $hilang->kronologi }}
            </div>

            <div class="mt-5 text-muted small text-center border-top pt-3">
                Dokumen digital ini divalidasi oleh Sistem Informasi Manajemen Aset. 
                Gunakan fitur ekspor PDF untuk mencetak fisik berita acara beserta verifikasi QR Code.
            </div>
        </div>
    </div>
</div>
@endsection