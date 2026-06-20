<div class="modal fade" id="importAssetModal" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <form action="{{ route('assets.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content shadow-lg">
                
                {{-- HEADER --}}
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-file-import mr-2"></i> Import Data Aset masal
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                {{-- BODY --}}
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Pilih File Excel</label>

                        <div class="custom-file">
                            <input type="file"
                                   class="custom-file-input"
                                   id="fileImportAsset"
                                   name="file"
                                   accept=".xls,.xlsx"
                                   required>
                            <label class="custom-file-label" for="fileImportAsset">
                                Pilih file (.xls / .xlsx)
                            </label>
                        </div>

                        <small class="text-muted d-block mt-3" style="font-size: 11.5px; line-height: 1.5;">
                            <i class="fas fa-info-circle text-info"></i> <b>Format Excel Terdeteksi (Format Asli BAZNAS):</b><br>
                            Sistem akan membaca otomatis mulai dari kolom Nama Barang, Merk, Ruangan, Kategori, Jenis, Sumber Dana, Tahun, dan Status (Kondisi).<br>
                            <span class="text-success font-weight-bold">
                                * Kolom Warna & Keterangan di Excel akan otomatis digabungkan ke dalam kolom Merk di sistem agar data tidak hilang.
                            </span>
                        </small>
                    </div>

                    <div class="alert alert-warning mt-3 mb-0" style="font-size: 11.5px;">
                        <i class="fas fa-exclamation-triangle"></i>
                        Pastikan penulisan nama referensi (Barang, Kategori, Lokasi, dll) di Excel <b>sama persis</b> dengan data yang ada di sistem master.
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-upload"></i> Mulai Import
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('fileImportAsset');
    if(fileInput) {
        fileInput.addEventListener('change', function (e) {
            const fileName = e.target.files[0]?.name || 'Pilih file...';
            e.target.nextElementSibling.innerText = fileName;
        });
    }
});
</script>