@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card shadow">

        <div class="card-body p-5">

            <div class="text-center mb-5">

                <h3 class="font-weight-bold">
                    BERITA ACARA
                </h3>

                <h5>
                    PERPINDAHAN ASET
                </h5>

                <hr>
            </div>

            <p>
                Pada hari ini telah dilakukan perpindahan aset
                dengan detail berikut:
            </p>

            <table class="table table-bordered">

                <tr>
                    <th width="30%">Kode Aset</th>
                    <td>{{ $pindah->asset->kode_aset }}</td>
                </tr>

                <tr>
                    <th>Nama Barang</th>
                    <td>{{ $pindah->asset->barang->nama_barang }}</td>
                </tr>

                <tr>
                    <th>Lokasi Sebelum</th>
                    <td>{{ $pindah->asset->lokasi->nama_lokasi }}</td>
                </tr>

                <tr>
                    <th>Lokasi Sesudah</th>
                    <td>{{ $pindah->lokasiBaru->nama_lokasi }}</td>
                </tr>

                <tr>
                    <th>PJ Sebelum</th>
                    <td>{{ $pindah->amilSebelum->nama }}</td>
                </tr>

                <tr>
                    <th>PJ Sesudah</th>
                    <td>{{ $pindah->amilSesudah->nama }}</td>
                </tr>

                <tr>
                    <th>Alasan</th>
                    <td>{{ $pindah->alasan }}</td>
                </tr>

                <tr>
                    <th>Tanggal Approval</th>
                    <td>
                        {{ $pindah->updated_at->format('d M Y H:i') }}
                    </td>
                </tr>

            </table>

            <br>

            <h5 class="font-weight-bold">
                Approval Digital Signature
            </h5>

            <table class="table table-bordered">

                <thead class="thead-light">

                    <tr class="text-center">
                        <th>Jabatan</th>
                        <th>Nama</th>
                        <th>Status</th>
                    </tr>

                </thead>

                <tbody>

                    @foreach($pindah->approvals as $approval)

                    <tr class="text-center">

                        <td>
                            @if($approval->stage == 1)
                                Kepala Departemen
                            @else
                                Kepala Divisi
                            @endif
                        </td>

                        <td>
                            {{ $approval->approvedBy->name ?? '-' }}
                        </td>

                        <td>

                            @if($approval->status == 'disetujui')

                                <span class="badge badge-success">
                                    Signed Digitally
                                </span>

                            @else

                                <span class="badge badge-danger">
                                    Ditolak
                                </span>

                            @endif

                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

            <div class="mt-5 text-muted small text-center">

                Dokumen ini dibuat otomatis oleh Sistem Informasi
                Manajemen Aset menggunakan Digital Signature RSA.

            </div>

        </div>

    </div>

</div>

@endsection