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
                    
                    <div class="alert alert-light border p-2 mb-3" style="font-size: 11.5px;">
                        <i class="fas fa-info-circle text-info"></i> Komponen utama (Barang, Kategori, Jenis, Lokasi, dan Sumber Dana) bersifat permanen karena terikat dengan susunan <strong>Kode Aset</strong>. Untuk memindahkan lokasi atau penanggung jawab, silakan gunakan menu resmi <strong>Perpindahan Aset</strong>.
                    </div>

                    {{-- ===== KODE ASET UTAMA ===== --}}
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="font-weight-bold">Kode Aset (Read-Only)</label>
                            <input type="text" id="edit_kode_aset" class="form-control font-weight-bold bg-light text-success" style="font-size: 14px; letter-spacing: 0.5px;" readonly>
                        </div>
                    </div>

                    <hr class="mt-0">

                    {{-- ===== DATA INFORMASI KOSMETIK YANG BOLEH DIEDIT ===== --}}
                    <h6 class="font-weight-bold text-warning mb-3"><i class="fas fa-cubes"></i> Detail Informasi Aset</h6>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label>Merk / Tipe</label>
                            <input type="text" name="merk" id="edit_merk" class="form-control" placeholder="Masukkan merk aset">
                        </div>

                        <div class="col-md-3 mb-2">
                            <label>Tahun Otorisasi</label>
                            <input type="text" name="tahun" id="edit_tahun" class="form-control bg-light" readonly>
                        </div>

                        <div class="col-md-3 mb-2">
                            <label>Volume / Jumlah</label>
                            <input type="number" name="volume" id="edit_volume" class="form-control" min="1" required>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Satuan</label>
                            <input type="text" name="satuan" id="edit_satuan" class="form-control" placeholder="Misal: Unit, Pcs, Buah">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Harga Satuan</label>
                            <input type="number" name="harga" id="edit_harga" class="form-control" min="0" required>
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Total Nilai Aset</label>
                            <input type="number" id="edit_total_harga" class="form-control bg-light" readonly>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Kondisi Barang</label>
                            <select name="kondisi" id="edit_kondisi" class="form-control" required>
                                <option value="baik">Baik</option>
                                <option value="rusak">Rusak</option>
                                <option value="hilang">Hilang</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Status Operasional</label>
                            <select name="status" id="edit_status" class="form-control" required>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning font-weight-bold text-dark">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Handle Edit Modal - Populate data terfilter
    document.querySelectorAll('.btn-edit-asset').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = btn.getAttribute('data-id');
            
            fetch(`/assets/${id}`)
                .then(response => response.json())
                .then(data => {
                    // Set form action route update
                    document.getElementById('editAssetForm').action = `/assets/${id}`;
                    
                    // Isi data field yang tersisa
                    document.getElementById('edit_kode_aset').value = data.kode_aset || '-';
                    document.getElementById('edit_merk').value = data.merk || '-';
                    document.getElementById('edit_tahun').value = data.tahun || '';
                    document.getElementById('edit_volume').value = data.volume || '1';
                    document.getElementById('edit_satuan').value = data.satuan || '';
                    document.getElementById('edit_harga').value = data.harga || '0';
                    document.getElementById('edit_total_harga').value = data.total_harga || '0';
                    document.getElementById('edit_kondisi').value = data.kondisi || 'baik';
                    document.getElementById('edit_status').value = data.status || 'aktif';
                    
                    // Trigger modal Bootstrap
                    $('#editAssetModal').modal('show');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal mengambil data modifikasi aset.');
                });
        });
    });

    // Otomatisasi perhitungan total harga finansial aset
    document.getElementById('edit_volume').addEventListener('input', hitungTotalEdit);
    document.getElementById('edit_harga').addEventListener('input', hitungTotalEdit);

    function hitungTotalEdit() {
        const v = parseFloat(document.getElementById('edit_volume').value) || 0;
        const h = parseFloat(document.getElementById('edit_harga').value) || 0;
        document.getElementById('edit_total_harga').value = v * h;
    }
});

</script>