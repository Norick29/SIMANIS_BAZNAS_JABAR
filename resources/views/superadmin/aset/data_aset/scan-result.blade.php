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

        {{-- FOOTER CARD (DINAMIS BERDASARKAN STATUS LOGIN) --}}
        <div class="card-footer bg-white">

            <div class="text-center mb-2">
                <small class="text-muted">
                    Dibuat: {{ $aset->created_at->format('d M Y H:i') }}
                </small>
            </div>

            @auth
                {{-- SKENARIO 1: JIKA USER SUDAH LOGIN --}}
                @if(auth()->user()->role !== 'kadept' && auth()->user()->role !== 'kadiv')
                    {{-- Jika yang login adalah STAF / SUPERADMIN, tombol fungsi asli tetap berjalan --}}
                    <button type="button" id="btnBukaModalPindah" class="btn btn-warning btn-block font-weight-bold">
                        <i class="fas fa-exchange-alt"></i> Ajukan Perpindahan Aset
                    </button>
                @else
                    {{-- Jika yang login adalah KADEPT / KADIV, tombol dimatikan dan diberi alert --}}
                    <div class="alert alert-info text-center border-info shadow-sm mb-0">
                        <i class="fas fa-info-circle text-info"></i> Anda login sebagai <strong>{{ strtoupper(auth()->user()->role) }}</strong>.<br>
                        <small>Menu pengajuan mutasi aset lapangan hanya diperuntukkan bagi Staff / Operator Logistik.</small>
                    </div>
                @endif
            @else
                {{-- SKENARIO 2: JIKA USER BELUM LOGIN --}}
                {{-- Biarkan tombol bridge muncul, karena sistem belum tahu siapa yang melakukan scan --}}
                <a href="{{ route('assets.scan.bridge') }}?target={{ urlencode(request()->fullUrl()) }}" class="btn btn-warning btn-block font-weight-bold">
                    <i class="fas fa-exchange-alt"></i> Ajukan Perpindahan Aset
                </a>
            @endauth

        </div>

    </div>

</div>

{{-- INCLUDE COMPONENT MODAL BAWAAN DASHBOARD KAMU --}}
@include('superadmin.aset.data_aset.modal-pindah-form')

{{-- JAVASCRIPT LOGIC --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    // === PENGATURAN PEMICU MODAL (MENGGUNAKAN SESSION LARAVEL, ANTI-403) ===
    
    // 1. Pemicu via Tombol Langsung (Jika user sudah posisi login di HP sejak awal)
    const btnBuka = document.getElementById('btnBukaModalPindah');
    if (btnBuka) {
        btnBuka.addEventListener('click', function() {
            bukaDanIsiModalPindah();
        });
    }

    // 2. Pemicu otomatis setelah kembali dari Halaman Login
    // Kita baca session flash Laravel langsung di dalam JavaScript
    @if(session('trigger_pindah_scan'))
        bukaDanIsiModalPindah();
    @endif

    // Fungsi inti untuk mengisi data dan menampilkan modal (Tetap utuh seperti kemarin)
    function bukaDanIsiModalPindah() {
        if (document.getElementById('pindah_id_aset')) {
            document.getElementById('pindah_id_aset').value = "{{ $aset->id_aset }}";
            document.getElementById('p_kode_aset').textContent = "{{ $aset->kode_aset }}";
            document.getElementById('p_barang').textContent = "{{ $aset->barang->nama_barang ?? '-' }}";
            document.getElementById('p_lokasi').textContent = "{{ $aset->lokasi->nama_lokasi ?? '-' }}";
            document.getElementById('p_amil').textContent = "{{ $aset->amil->nama ?? '-' }}";

            // Buka modal secara paksa
            $('#pindahFormModal').modal('show');
        }
    }

    // ==========================================
    // INIT SELECT2 (Kodingan asli kamu tetap utuh)
    // ==========================================
    $('#pindahFormModal').on('shown.bs.modal', function () {
        if (!$('.select-amil-pindah').hasClass('select2-hidden-accessible')) {
            $('.select-amil-pindah').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: 'Cari penanggung jawab...',
                dropdownParent: $('#pindahFormModal')
            });
        }

        if (!$('.select-lokasi-pindah').hasClass('select2-hidden-accessible')) {
            $('.select-lokasi-pindah').select2({
                theme: 'bootstrap4',
                width: '100%',
                placeholder: 'Cari lokasi...',
                dropdownParent: $('#pindahFormModal')
            });
        }
    });

    // ==========================================
    // SUBMIT FORM AJAX FETCH (Kodingan asli kamu tetap utuh)
    // ==========================================
    document.getElementById('pindahAsetForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('{{ route("assets.pindah") }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                $('#pindahFormModal').modal('hide');
                window.location.href = "{{ route('reports.index') }}";
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error(error);
            alert('Terjadi kesalahan');
        });
    });
});
</script>
@endsection