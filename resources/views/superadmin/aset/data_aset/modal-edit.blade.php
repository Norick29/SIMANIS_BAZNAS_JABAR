<div class="modal fade" id="editAssetModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg">

            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-edit mr-2"></i> Edit Data Aset
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form id="editAssetForm" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">

                    {{-- ===== DATA REFERENSI ===== --}}
                    <h6 class="font-weight-bold text-warning mb-2">Data Referensi</h6>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label>Barang</label>
                            <select name="id_barang" id="edit_id_barang" class="form-control select-barang-edit" required>
                                <option value="">-- Pilih Barang --</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Kategori</label>
                            <select name="id_kategori" id="edit_id_kategori" class="form-control" required>
                                <option value="">-- Pilih Kategori --</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Jenis</label>
                            <select name="id_jenis" id="edit_id_jenis" class="form-control select-jenis-edit" required>
                                <option value="">-- Pilih Jenis --</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Lokasi</label>
                            <select name="id_lokasi" id="edit_id_lokasi" class="form-control select-lokasi-edit" required>
                                <option value="">-- Pilih Lokasi --</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Penanggung Jawab (Amil)</label>
                            <select name="id_amil" id="edit_id_amil" class="form-control select-amil-edit" required>
                                <option value="">-- Pilih Amil --</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Sumber Dana</label>
                            <select name="id_sumber_dana" id="edit_id_sumber_dana" class="form-control" required>
                                <option value="">-- Pilih Sumber Dana --</option>
                            </select>
                        </div>
                    </div>

                    <hr>

                    {{-- ===== DATA ASET ===== --}}
                    <h6 class="font-weight-bold text-warning mb-2">Detail Aset</h6>

                    <div class="row">
                        <div class="col-md-12 mb-2">
                            <label>Kode Aset</label>
                            <input type="text" id="edit_kode_aset" class="form-control" readonly>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Merk</label>
                            <input type="text" name="merk" id="edit_merk" class="form-control">
                        </div>

                        <div class="col-md-3 mb-2">
                            <label>Tahun</label>
                            <input type="text" name="tahun" id="edit_tahun" class="form-control" maxlength="4">
                        </div>

                        <div class="col-md-3 mb-2">
                            <label>Volume</label>
                            <input type="number" name="volume" id="edit_volume" class="form-control" value="1" min="1">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Satuan</label>
                            <input type="text" name="satuan" id="edit_satuan" class="form-control">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Harga</label>
                            <input type="number" name="harga" id="edit_harga" class="form-control" min="0">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Total Harga</label>
                            <input type="number" id="edit_total_harga" class="form-control" readonly>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Kondisi</label>
                            <select name="kondisi" id="edit_kondisi" class="form-control">
                                <option value="baik">Baik</option>
                                <option value="rusak">Rusak</option>
                                <option value="hilang">Hilang</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Status</label>
                            <select name="status" id="edit_status" class="form-control">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Handle Edit Modal - Populate data
    document.querySelectorAll('.btn-edit-asset').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = btn.getAttribute('data-id');
            
            fetch(`/assets/${id}`)
                .then(response => response.json())
                .then(data => {
                    // Set form action
                    document.getElementById('editAssetForm').action = `/assets/${id}`;
                    
                    // Populate fields
                    document.getElementById('edit_kode_aset').value = data.kode_aset || '-';
                    document.getElementById('edit_merk').value = data.merk || '-';
                    document.getElementById('edit_tahun').value = data.tahun || '';
                    document.getElementById('edit_volume').value = data.volume || '1';
                    document.getElementById('edit_satuan').value = data.satuan || '';
                    document.getElementById('edit_harga').value = data.harga || '0';
                    document.getElementById('edit_total_harga').value = data.total_harga || '0';
                    document.getElementById('edit_kondisi').value = data.kondisi || 'baik';
                    document.getElementById('edit_status').value = data.status || 'aktif';
                    
                    // Set select values (kamu perlu sesuaikan dengan data yang dikembalikan)
                    document.getElementById('edit_id_barang').value = data.id_barang || '';
                    document.getElementById('edit_id_kategori').value = data.id_kategori || '';
                    document.getElementById('edit_id_jenis').value = data.id_jenis || '';
                    document.getElementById('edit_id_lokasi').value = data.id_lokasi || '';
                    document.getElementById('edit_id_amil').value = data.id_amil || '';
                    document.getElementById('edit_id_sumber_dana').value = data.id_sumber_dana || '';
                    
                    // Trigger modal
                    $('#editAssetModal').modal('show');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat data aset');
                });
        });
    });

    // Hitung total harga saat volume atau harga berubah
    document.getElementById('edit_volume').addEventListener('input', function() {
        hitungTotalEdit();
    });
    document.getElementById('edit_harga').addEventListener('input', function() {
        hitungTotalEdit();
    });

    function hitungTotalEdit() {
        const v = document.getElementById('edit_volume').value || 0;
        const h = document.getElementById('edit_harga').value || 0;
        document.getElementById('edit_total_harga').value = v * h;
    }

    // Initialize select2 saat modal dibuka
    $('#editAssetModal').on('shown.bs.modal', function() {
        if (!$('.select-barang-edit').hasClass('select2-hidden-accessible')) {
            $('.select-barang-edit').select2({
                theme: 'bootstrap4',
                placeholder: 'Cari barang...',
                width: '100%',
                dropdownParent: $('#editAssetModal')
            });

            $('.select-jenis-edit').select2({
                theme: 'bootstrap4',
                placeholder: 'Cari jenis...',
                width: '100%',
                dropdownParent: $('#editAssetModal')
            });

            $('.select-lokasi-edit').select2({
                theme: 'bootstrap4',
                placeholder: 'Cari lokasi...',
                width: '100%',
                dropdownParent: $('#editAssetModal')
            });

            $('.select-amil-edit').select2({
                theme: 'bootstrap4',
                placeholder: 'Cari penanggung jawab...',
                width: '100%',
                dropdownParent: $('#editAssetModal')
            });
        }
    });

});
</script>