<div class="modal fade" id="detailAsetModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Detail Aset</h5>

                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <div class="text-center mb-4">
                    <img id="detail_qr"
                         width="180"
                         class="img-thumbnail rounded"
                         style="display:block; margin:auto;"
                         alt="QR Code">
                </div>

                <hr>

                <table class="table table-sm table-bordered">

                    <tr><th>Kode Aset</th><td id="d_kode"></td></tr>
                    <tr><th>Barang</th><td id="d_barang"></td></tr>
                    <tr><th>Merk</th><td id="d_merk"></td></tr>
                    <tr><th>Tahun</th><td id="d_tahun"></td></tr>
                    <tr><th>Volume</th><td id="d_volume"></td></tr>
                    <tr><th>Satuan</th><td id="d_satuan"></td></tr>
                    <tr><th>Kondisi</th><td id="d_kondisi"></td></tr>
                    <tr><th>Status</th><td id="d_status"></td></tr>
                    <tr><th>Harga</th><td id="d_harga"></td></tr>
                    <tr><th>Total Harga</th><td id="d_total"></td></tr>
                    <tr><th>Kategori</th><td id="d_kategori"></td></tr>
                    <tr><th>Jenis</th><td id="d_jenis"></td></tr>
                    <tr><th>Lokasi</th><td id="d_lokasi"></td></tr>
                    <tr><th>Penanggung Jawab</th><td id="d_amil"></td></tr>
                    <tr><th>Sumber Dana</th><td id="d_sumber"></td></tr>

                </table>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    id="btnPindahAset"
                    class="btn btn-warning btnPindahAset"
                    data-id-aset=""
                    data-kode-aset=""
                    data-barang=""
                    data-lokasi=""
                    data-amil="">
                    <i class="fas fa-exchange-alt"></i>
                    Pindah Aset
                </button>

                <button class="btn btn-secondary" data-dismiss="modal">
                    Tutup
                </button>

            </div>

        </div>
    </div>
</div>

@include('superadmin.aset.data_aset.modal-pindah-form')