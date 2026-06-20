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
                @if(auth()->user()->role !== 'kadept' && auth()->user()->role !== 'kadiv')
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
                @endif

                <button class="btn btn-secondary" data-dismiss="modal">
                    Tutup
                </button>

            </div>

        </div>
    </div>
</div>

@include('superadmin.aset.data_aset.modal-pindah-form')

<script>
$(document).ready(function() {
    // Sesuaikan pemicu klik tombol detail (menggunakan class gabungan agar aman)
    $(document).on('click', '.btn-detail-aset, .btn-view-detail', function() {
        let idAset = $(this).data('id');

        // Bersihkan gambar modal sebelum load baru (biar gak nge-flick)
        $('#detail_qr').attr('src', '').hide();

        // Lakukan request AJAX ke route show controller yang benar (tanpa /json)
        $.ajax({
            url: `/assets/${idAset}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                // Handle jika response dibungkus status success
                let aset = response.status === 'success' ? response : response;

                // 1. Menampilkan Gambar QR Code dari Path Database
                if(aset.qr_code) {
                    // Jika path sudah berupa URL utuh dari asset(), langsung pasang
                    let qrUrl = aset.qr_code.startsWith('http') ? aset.qr_code : window.location.origin + '/' + aset.qr_code;
                    $('#detail_qr').attr('src', qrUrl + '?t=' + new Date().getTime()).show();
                }

                // 2. Isi data teks tabel modal (Langsung panggil nama properti string-nya, bro!)
                $('#d_kode').text(aset.kode_aset || '-');
                $('#d_barang').text(aset.barang || '-');
                $('#d_merk').text(aset.merk || '-');
                $('#d_tahun').text(aset.tahun || '-');
                $('#d_volume').text(aset.volume || '-');
                $('#d_satuan').text(aset.satuan || '-');
                $('#d_kondisi').text(aset.kondisi || '-');
                $('#d_status').text(aset.status || '-');
                
                // Karena dari Controller sudah berformat Rupiah, langsung text() saja tanpa Intl.NumberFormat!
                $('#d_harga').text(aset.harga.includes('Rp') ? aset.harga : 'Rp ' + aset.harga);
                $('#d_total').text(aset.total_harga.includes('Rp') ? aset.total_harga : 'Rp ' + aset.total_harga);
                
                $('#d_kategori').text(aset.kategori || '-');
                $('#d_jenis').text(aset.jenis || '-');
                $('#d_lokasi').text(aset.lokasi || '-');
                $('#d_amil').text(aset.amil || '-');
                $('#d_sumber').text(aset.sumber_dana || '-');

                // 3. Update data-atribut pada tombol "Pindah Aset" di dalam modal
                $('#btnPindahAset').attr('data-id-aset', aset.id_aset);
                $('#btnPindahAset').attr('data-kode-aset', aset.kode_aset);
                $('#btnPindahAset').attr('data-barang', aset.barang);
                $('#btnPindahAset').attr('data-lokasi', aset.lokasi);
                $('#btnPindahAset').attr('data-amil', aset.amil);

                // Tampilkan modalnya
                $('#detailAsetModal').modal('show');
            },
            error: function(xhr) {
                console.error(xhr);
                alert('Gagal mengambil data detail aset terbaru. Status: ' + xhr.status);
            }
        });
    });
});
</script>