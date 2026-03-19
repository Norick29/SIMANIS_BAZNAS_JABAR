<div class="modal fade" id="detailAsetModal{{ $aset->id_aset }}" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Detail Aset</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <div class="text-center mb-3">
                    @if (!empty($aset->qr_code))
                        <img src="{{ asset($aset->qr_code) }}" width="150" alt="QR Code">

                    @endif
                </div>

                <table class="table table-sm table-bordered">
                    <tr><th>Kode Aset</th><td>{{ $aset->kode_aset }}</td></tr>
                    <tr><th>Barang</th><td>{{ $aset->barang->nama_barang ?? '-' }}</td></tr>
                    <tr><th>Merk</th><td>{{ $aset->merk ?? '-' }}</td></tr>
                    <tr><th>Tahun</th><td>{{ $aset->tahun ?? '-' }}</td></tr>
                    <tr><th>Volume</th><td>{{ $aset->volume ?? '-' }}</td></tr>
                    <tr><th>Satuan</th><td>{{ $aset->satuan ?? '-' }}</td></tr>
                    <tr><th>Kondisi</th><td>{{ $aset->kondisi ?? '-' }}</td></tr>
                    <tr><th>Status</th><td>{{ $aset->status }}</td></tr>
                    <tr><th>Harga</th><td>{{ $aset->harga ?? '-' }}</td></tr>
                    <tr><th>Total Harga</th><td>{{ $aset->total_harga ?? '-' }}</td></tr>
                    <tr><th>Kategori</th><td>{{ $aset->kategori->nama_kategori ?? '-' }}</td></tr>
                    <tr><th>Jenis</th><td>{{ $aset->jenis->nama_jenis ?? '-' }}</td></tr>
                    <tr><th>Lokasi</th><td>{{ $aset->lokasi->nama_lokasi ?? '-' }}</td></tr>
                    <tr><th>Penanggung Jawab</th><td>{{ $aset->amil->nama ?? '-' }}</td></tr>
                    <tr><th>Sumber Dana</th><td>{{ $aset->sumberDana->nama_sumber_dana ?? '-' }}</td></tr>
                </table>

            </div>

            {{-- FOOTER + TOMBOL --}}
            <div class="modal-footer">
                <button class="btn btn-warning"
                    data-toggle="modal"
                    data-target="#pindahAsetModal"
                    onclick="document.getElementById('pindah_id_aset').value = {{ $aset->id_aset }}">
                    <i class="fas fa-exchange-alt"></i> Pindah Aset
                </button>

                <button class="btn btn-secondary" data-dismiss="modal">
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>

@include('superadmin.aset.data_aset.modal-pindah')

{{-- <script>
$(document).on('click', '.btnPindahAset', function () {
    let idAset = $(this).data('id');

    // tutup semua modal detail
    $('.modal').modal('hide');

    // set id_aset ke form pindah
    $('#pindahAsetForm input[name="id_aset"]').val(idAset);

    // buka modal pindah aset
    setTimeout(() => {
        $('#pindahAsetModal').modal('show');
    }, 400);
}); --}}

<script>
$(document).on('click', '.btnPindahAset', function () {

    let idAset = $(this).data('id');

    // isi hidden input
    $('#pindah_id_aset').val(idAset);

    // tutup modal detail
    $('.modal').modal('hide');

    // buka modal pindah
    setTimeout(function() {
        $('#pindahAsetModal').modal('show');
    }, 400);

});
</script>

