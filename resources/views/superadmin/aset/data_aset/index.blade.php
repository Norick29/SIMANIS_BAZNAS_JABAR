@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800"><i class="fas fa-fw fa-box"></i> Manajemen Aset</h1>

    {{-- ALERT SUCCESS --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    {{-- ALERT WARNING --}}
    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            {{ session('warning') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    {{-- DUPLICATE IMPORT --}}
    @if (session('duplicates'))
        <div class="alert alert-danger" style="font-size: 13px;">
            <h6 class="font-weight-bold">
                <i class="fas fa-ban mr-2"></i>
                Beberapa baris gagal di-import karena data Master belum ada di aplikasi:
            </h6>
            <ul class="mb-0 pl-3">
                @foreach (session('duplicates') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <small class="d-block mt-2 text-dark">
                *Silahkan input dulu nama data master tersebut di menu Master Data, lalu upload ulang baris yang gagal tadi.
            </small>
        </div>
    @endif

    {{-- ALERT ERROR --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-times-circle mr-2"></i>
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    {{-- CARD UTAMA --}}
    <div class="card shadow mb-4">
        {{-- HEADER --}}
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2">
                @if(auth()->user()->role !== 'kadept' && auth()->user()->role !== 'kadiv')
                    <a href="#" class="btn btn-success btn-sm mr-2" data-toggle="modal" data-target="#createAssetModal">
                        <i class="fas fa-plus"></i> Tambah Asset
                    </a>
                    <a href="#" class="btn btn-info btn-sm mr-2" data-toggle="modal" data-target="#importAssetModal">
                        <i class="fas fa-file-excel"></i> Import Excel
                    </a>
                @endif
                
                <button type="button" id="btnPrintSelected" class="btn btn-warning btn-sm font-weight-bold mr-2">
                    <i class="fas fa-print"></i> Cetak QR Terpilih
                </button>

                <a href="{{ route('assets.bulk-print-qr', request()->query()) }}" target="_blank" class="btn btn-primary btn-sm font-weight-bold mr-2">
                    <i class="fas fa-qrcode"></i> Cetak Massal QR Code
                </a>

                <button class="btn btn-secondary btn-sm" type="button" data-toggle="collapse" data-target="#filterAsset">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>

            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-list"></i> Daftar Aset
            </h6>
        </div>

        {{-- BODY --}}
        <div class="card-body">
            {{-- TOGGLE COLLAPSE FILTER --}}
            <div class="collapse mb-3 {{ request()->hasAny(['id_kategori', 'id_lokasi', 'tahun', 'kondisi']) ? 'show' : '' }}" id="filterAsset">
                <div class="card border-left-primary shadow-sm">
                    <div class="card-body">
                        <form method="GET" action="{{ route('assets.index') }}">
                            <div class="row">
                                {{-- KATEGORI --}}
                                <div class="col-md-3">
                                    <label>Kategori</label>
                                    <select name="id_kategori" class="form-control">
                                        <option value="">Semua Kategori</option>
                                        @foreach($kategoris as $kategori)
                                            <option value="{{ $kategori->id_kategori }}" {{ request('id_kategori') == $kategori->id_kategori ? 'selected' : '' }}>
                                                {{ $kategori->nama_kategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- LOKASI --}}
                                <div class="col-md-3">
                                    <label>Lokasi</label>
                                    <select name="id_lokasi" class="form-control">
                                        <option value="">Semua Lokasi</option>
                                        @foreach($lokasis as $lokasi)
                                            <option value="{{ $lokasi->id_lokasi }}" {{ request('id_lokasi') == $lokasi->id_lokasi ? 'selected' : '' }}>
                                                {{ $lokasi->nama_lokasi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- TAHUN --}}
                                <div class="col-md-3">
                                    <label>Tahun</label>
                                    <input type="number" name="tahun" class="form-control" value="{{ request('tahun') }}">
                                </div>

                                {{-- KONDISI --}}
                                <div class="col-md-3">
                                    <label>Kondisi</label>
                                    <select name="kondisi" class="form-control">
                                        <option value="">Semua Kondisi</option>
                                        <option value="baik" {{ request('kondisi') == 'baik' ? 'selected' : '' }}>Baik</option>
                                        <option value="rusak" {{ request('kondisi') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                                        <option value="hilang" {{ request('kondisi') == 'hilang' ? 'selected' : '' }}>Hilang</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-3 text-right">
                                <a href="{{ route('assets.index') }}" class="btn btn-secondary">Reset</a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-search"></i> Terapkan Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- TABEL DATA --}}
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th width="40"><input type="checkbox" id="checkAll"></th>
                            <th>No</th>
                            <th>Kode Aset</th>
                            <th>Barang</th>
                            <th>Kategori</th>
                            <th>Jenis</th>
                            <th>Lokasi</th>
                            <th>Penanggung Jawab</th>
                            <th>Sumber Dana</th>
                            <th>Kondisi</th>
                            <th>Status</th>
                            <th>{{ (auth()->user()->role == 'kadept' || auth()->user()->role == 'kadiv') ? 'Detail' : 'Aksi' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($assets as $aset)
                            <tr class="text-center">
                                <td>
                                    <input type="checkbox" class="asset-checkbox" value="{{ $aset->id_aset }}">
                                </td>
                                <td>
                                    {{ $assets->firstItem() + $loop->index }}
                                </td>
                                <td class="font-weight-bold text-success">
                                    {{ $aset->kode_aset }}
                                </td>
                                <td>
                                    {{ $aset->barang->nama_barang ?? '-' }}
                                </td>
                                <td>
                                    <span class="badge badge-primary">
                                        {{ $aset->kategori->nama_kategori ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    {{ $aset->jenis->nama_jenis ?? '-' }}
                                </td>
                                <td>
                                    {{ $aset->lokasi->nama_lokasi ?? '-' }}
                                </td>
                                <td>
                                    {{ $aset->amil->nama ?? '-' }}
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $aset->sumberDana->nama_sumber_dana ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $aset->kondisi == 'baik' ? 'success' : ($aset->kondisi == 'rusak' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($aset->kondisi) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">
                                        {{ ucfirst($aset->status) }}
                                    </span>
                                </td>
                                <td>
                                    {{-- DETAIL --}}
                                    <button class="btn btn-info btn-sm btn-detail-aset" data-toggle="modal" data-target="#detailAsetModal" data-id="{{ $aset->id_aset }}">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    @if(auth()->user()->role !== 'kadept' && auth()->user()->role !== 'kadiv')    
                                        {{-- EDIT --}}
                                        <button type="button" class="btn btn-warning btn-sm btn-edit-asset" data-toggle="modal" data-target="#editAssetModal" data-id="{{ $aset->id_aset }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        {{-- DELETE --}}
                                        <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteAssetModal" data-id="{{ $aset->id_aset }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted">
                                    Data aset belum tersedia
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- PAGINATION --}}
                <div class="row mt-3">
                    <div class="col-md-6 text-muted">
                        Showing {{ $assets->firstItem() ?? 0 }} to {{ $assets->lastItem() ?? 0 }} of {{ $assets->total() }} entries
                    </div>
                    <div class="col-md-6 d-flex justify-content-end">
                        {{ $assets->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('superadmin.aset.data_aset.modal-create')
@include('superadmin.aset.data_aset.modal-detail')
@include('superadmin.aset.data_aset.modal-edit')
@include('superadmin.aset.data_aset.modal-delete')
@include('superadmin.aset.data_aset.modal-import')

{{-- INTEGRASI JAVASCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // =========================================
    // DETAIL ASET
    // =========================================
    $('.btn-detail-aset').on('click', function () {
        let id = $(this).data('id');
        fetch(`/assets/${id}`)
            .then(response => response.json())
            .then(data => {
                // INJECT DETAIL TEXT
                $('#d_kode').text(data.kode_aset || '-');
                $('#d_barang').text(data.barang || '-');
                $('#d_merk').text(data.merk || '-');
                $('#d_tahun').text(data.tahun || '-');
                $('#d_volume').text(data.volume || '-');
                $('#d_satuan').text(data.satuan || '-');
                $('#d_kondisi').text(data.kondisi || '-');
                $('#d_status').text(data.status || '-');
                $('#d_harga').text(data.harga || '-');
                $('#d_total').text(data.total_harga || '-');
                $('#d_kategori').text(data.kategori || '-');
                $('#d_jenis').text(data.jenis || '-');
                $('#d_lokasi').text(data.lokasi || '-');
                $('#d_amil').text(data.amil || '-');
                $('#d_sumber').text(data.sumber_dana || '-');

                // TRACKING IMAGE QR
                if (data.qr_code) {
                    $('#detail_qr').attr('src', data.qr_code).show();
                } else {
                    $('#detail_qr').hide();
                }

                // SIMPAN DATA KE BUTTON PINDAH
                $('#btnPindahAset')
                    .attr('data-id-aset', data.id_aset)
                    .attr('data-kode-aset', data.kode_aset)
                    .attr('data-barang', data.barang)
                    .attr('data-lokasi', data.lokasi)
                    .attr('data-amil', data.amil);

                // SHOW MODAL
                $('#detailAsetModal').modal('show');
            })
            .catch(error => {
                console.error(error);
                alert('Gagal memuat detail aset');
            });
    });

    // =========================================
    // PINDAH ASET
    // =========================================
    $(document).on('click', '#btnPindahAset', function () {
        let idAset   = $(this).attr('data-id-aset');
        let kodeAset = $(this).attr('data-kode-aset');
        let barang   = $(this).attr('data-barang');
        let lokasi   = $(this).attr('data-lokasi');
        let amil     = $(this).attr('data-amil');

        // INJECT DATA KE MODAL PINDAH
        $('#pindah_id_aset').val(idAset);
        $('#p_kode_aset').text(kodeAset);
        $('#p_barang').text(barang);
        $('#p_lokasi').text(lokasi);
        $('#p_amil').text(amil);

        // TUTUP DETAIL & TUNGGU HINGGA MODAL SELESAI SAKLAR HIDE
        $('#detailAsetModal').modal('hide');
        $('#detailAsetModal').one('hidden.bs.modal', function () {
            $('#pindahFormModal').modal('show');
        });
    });

    // =========================================
    // CHECKBOX BULK ACTIONS
    // =========================================
    $('#checkAll').on('change', function () {
        $('.asset-checkbox').prop('checked', $(this).is(':checked'));
    });

    $('#btnPrintSelected').on('click', function () {
        let ids = [];
        $('.asset-checkbox:checked').each(function () {
            ids.push($(this).val());
        });

        if(ids.length === 0) {
            alert('Pilih minimal satu aset.');
            return;
        }

        let form = $('<form>', {
            method: 'POST',
            action: '{{ route("assets.selected-print-qr") }}',
            target: '_blank'
        });

        form.append($('<input>', {
            type: 'hidden',
            name: '_token',
            value: '{{ csrf_token() }}'
        }));

        ids.forEach(function(id){
            form.append($('<input>', {
                type: 'hidden',
                name: 'asset_ids[]',
                value: id
            }));
        });

        $('body').append(form);
        form.submit();
    });
});
</script>
@endsection