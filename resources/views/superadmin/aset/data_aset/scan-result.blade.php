@extends('layouts.public')

@section('content')

<div class="container py-3" style="max-width:720px;">

    {{-- HEADER --}}
    <div class="text-center mb-3">
        <h5 class="mb-0">Hasil Scan QR Code</h5>
        <small class="text-muted">Informasi aset</small>
    </div>

    {{-- CARD --}}
    <div class="card shadow-sm border-0">

        {{-- HEADER CARD --}}
        <div class="card-header bg-success text-white text-center">
            <strong><i class="fas fa-box"></i> Detail Aset</strong>
        </div>

        <div class="card-body">

            {{-- QR + KODE (MOBILE PRIORITY) --}}
            <div class="text-center mb-3">
                @if ($aset->qr_code)
                    <img src="{{ asset($aset->qr_code) }}" 
                         class="img-fluid mb-2"
                         style="max-width:140px;">
                @endif

                <div class="font-weight-bold text-success">
                    {{ $aset->kode_aset }}
                </div>

                <div class="font-weight-bold">
                    {{ $aset->barang->nama_barang ?? '-' }}
                </div>

                <div class="mt-2">
                    <span class="badge badge-{{ $aset->status == 'aktif' ? 'primary' : 'secondary' }}">
                        {{ ucfirst($aset->status) }}
                    </span>

                    <span class="badge badge-{{ $aset->kondisi == 'baik' ? 'success' : ($aset->kondisi == 'rusak' ? 'warning' : 'danger') }}">
                        {{ ucfirst($aset->kondisi) }}
                    </span>
                </div>
            </div>

            <hr>

            {{-- DETAIL GRID --}}
            <div class="row">

                <div class="col-6 mb-2">
                    <small class="text-muted">Merk</small>
                    <div class="font-weight-bold">{{ $aset->merk ?? '-' }}</div>
                </div>

                <div class="col-6 mb-2">
                    <small class="text-muted">Tahun</small>
                    <div class="font-weight-bold">{{ $aset->tahun ?? '-' }}</div>
                </div>

                <div class="col-6 mb-2">
                    <small class="text-muted">Kategori</small>
                    <div>{{ $aset->kategori->nama_kategori ?? '-' }}</div>
                </div>

                <div class="col-6 mb-2">
                    <small class="text-muted">Jenis</small>
                    <div>{{ $aset->jenis->nama_jenis ?? '-' }}</div>
                </div>

                <div class="col-6 mb-2">
                    <small class="text-muted">Volume</small>
                    <div>{{ $aset->volume ?? '-' }} {{ $aset->satuan ?? '' }}</div>
                </div>

                <div class="col-6 mb-2">
                    <small class="text-muted">Harga</small>
                    <div>Rp {{ number_format($aset->harga, 0, ',', '.') ?? '-' }}</div>
                </div>

                <div class="col-12 mb-2">
                    <small class="text-muted">Total Harga</small>
                    <div class="text-success font-weight-bold">
                        Rp {{ number_format($aset->total_harga, 0, ',', '.') ?? '-' }}
                    </div>
                </div>

            </div>

            <hr>

            {{-- LOKASI --}}
            <div class="mb-2">
                <small class="text-muted">Lokasi</small>
                <div class="font-weight-bold">{{ $aset->lokasi->nama_lokasi ?? '-' }}</div>
            </div>

            <div class="mb-2">
                <small class="text-muted">Penanggung Jawab</small>
                <div class="font-weight-bold">{{ $aset->amil->nama ?? '-' }}</div>
            </div>

            <div>
                <small class="text-muted">Sumber Dana</small>
                <div>{{ $aset->sumberDana->nama_sumber_dana ?? '-' }}</div>
            </div>

        </div>

        {{-- FOOTER --}}
        <div class="card-footer bg-white">

            <div class="text-center mb-2">
                <small class="text-muted">
                    Dibuat: {{ $aset->created_at->format('d M Y H:i') }}
                </small>
            </div>

            <button class="btn btn-warning btn-block"
                    data-toggle="modal"
                    data-target="#verifyLoginModal">
                <i class="fas fa-exchange-alt"></i> Pindah Aset
            </button>

        </div>

    </div>

</div>

{{-- MODAL --}}
{{-- @include('superadmin.aset.data_aset.modal-verify-login') --}}
{{-- @include('superadmin.aset.data_aset.modal-pindah-form') --}}

@endsection