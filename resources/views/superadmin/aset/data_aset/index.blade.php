@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-2 text-gray-800">Manajemen Aset</h1>

    {{-- ERROR --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    {{-- SESSION ALERT --}}
    @foreach (['success', 'error', 'warning'] as $type)
        @if (session($type))
            <div class="alert alert-{{ $type == 'error' ? 'danger' : $type }} alert-dismissible fade show">
                {{ session($type) }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif
    @endforeach

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex gap-2">
                <a href="#" class="btn btn-success btn-sm mr-2"
                data-toggle="modal"
                data-target="#createAssetModal">
                    <i class="fas fa-plus"></i> Tambah Asset
                </a>

                <a href="#" class="btn btn-info btn-sm"
                   data-toggle="modal"
                   data-target="#importAssetModal">
                    <i class="fas fa-file-excel"></i> Import Excel
                </a>
            </div>

            <h6 class="m-0 font-weight-bold text-success">Daftar Aset</h6>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr class="text-center">
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
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($assets as $aset)
                        <tr class="text-center">
                            <td>{{ $assets->firstItem() + $loop->index }}</td>
                            <td class="font-weight-bold text-success">
                                {{ $aset->kode_aset }}
                            </td>
                            <td>{{ $aset->barang->nama_barang ?? '-' }}</td>
                            <td>
                                <span class="badge badge-primary">
                                    {{ $aset->kategori->nama_kategori ?? '-' }}
                                </span>
                            </td>
                            <td>{{ $aset->jenis->nama_jenis ?? '-' }}</td>
                            <td>{{ $aset->lokasi->nama_lokasi ?? '-' }}</td>
                            <td>{{ $aset->amil->nama ?? '-' }}</td>
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
                                <button class="btn btn-info btn-sm btn-detail-aset"
                                    data-toggle="modal"
                                    data-target="#detailAsetModal"
                                    data-id="{{ $aset->id_aset }}">
                                    <i class="fas fa-eye"></i>
                                </button>

                                {{-- EDIT --}}
                                <button type="button"
                                    class="btn btn-warning btn-sm btn-edit-asset"
                                    data-toggle="modal"
                                    data-target="#editAssetModal"
                                    data-id="{{ $aset->id_aset }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                {{-- DELETE --}}
                                <button class="btn btn-danger btn-sm"
                                    data-toggle="modal"
                                    data-target="#deleteAssetModal"
                                    data-id="{{ $aset->id_aset }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted">
                                Data aset belum tersedia
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- PAGINATION --}}
                <div class="row mt-3">
                    <div class="col-md-6 text-muted">
                        Showing {{ $assets->firstItem() }} to {{ $assets->lastItem() }}
                        of {{ $assets->total() }} entries
                    </div>

                    <div class="col-md-6 d-flex justify-content-end">
                        {{ $assets->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- @include('assets.modal-import')
@include('assets.modal-delete') --}}
@include('superadmin.aset.data_aset.modal-create')
@include('superadmin.aset.data_aset.modal-detail')
@include('superadmin.aset.data_aset.modal-edit')
@include('superadmin.aset.data_aset.modal-delete')

@endsection

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

                // DETAIL
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

                // QR
                if (data.qr_code) {

                    $('#detail_qr')
                        .attr('src', data.qr_code)
                        .show();

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

        let idAset = $(this).attr('data-id-aset');

        let kodeAset = $(this).attr('data-kode-aset');

        let barang = $(this).attr('data-barang');

        let lokasi = $(this).attr('data-lokasi');

        let amil = $(this).attr('data-amil');

        // INJECT DATA KE MODAL PINDAH
        $('#pindah_id_aset').val(idAset);

        $('#p_kode_aset').text(kodeAset);

        $('#p_barang').text(barang);

        $('#p_lokasi').text(lokasi);

        $('#p_amil').text(amil);

        // TUTUP DETAIL
        $('#detailAsetModal').modal('hide');

        // TUNGGU MODAL DETAIL TERTUTUP
        $('#detailAsetModal').one('hidden.bs.modal', function () {

            // SHOW MODAL PINDAH
            $('#pindahFormModal').modal('show');

        });

    });

});

</script>
