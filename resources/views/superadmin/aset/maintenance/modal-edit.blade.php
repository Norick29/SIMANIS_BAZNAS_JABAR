@foreach ($maintenances as $m)
<div class="modal fade" id="editMaintenanceModal{{ $m->id_maintenance }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg">

            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-tools mr-2"></i> Form Penyelesaian Perawatan Aset
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form action="{{ route('maintenances.update', $m->id_maintenance) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    
                    {{-- INFORMASI ASET (READ ONLY) --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Aset Yang Diperiksa</label>
                        <input type="text" class="form-control bg-light font-weight-bold text-muted" 
                               value="{{ $m->asset->kode_aset ?? '-' }} - {{ $m->asset->barang->nama_barang ?? '-' }}" readonly>
                    </div>

                    {{-- INPUT 1: TINGKAT KERUSAKAN (USULAN BRILIAN KAMU) --}}
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Tingkat Kerusakan Fisik</label>
                        <select name="tingkat_kerusakan" class="form-control mb-1" required>
                            <option value="ringan">Ringan (Estimasi Selesai 1 Hari)</option>
                            <option value="sedang" selected>Sedang (Estimasi Selesai 3 Hari)</option>
                            <option value="berat">Berat (Estimasi Selesai 7 Hari)</option>
                        </select>
                        <small class="text-muted"><i class="fas fa-info-circle text-info"></i> Sistem akan menghitung deviasi ketepatan estimasi ini secara otomatis.</small>
                    </div>

                    {{-- INPUT 2: BIAYA RIIL NOTA --}}
                    <div class="form-group">
                        <label class="font-weight-bold text-success">Biaya Perbaikan Riil (Sesuai Kuitansi)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-success text-white">Rp</span>
                            </div>
                            <input type="number" name="biaya" class="form-control font-weight-bold" 
                                   placeholder="Masukkan total biaya di nota asli" min="0" required>
                        </div>
                    </div>

                    {{-- INPUT 3: LOG HASIL --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Keterangan / Tindakan Perbaikan</label>
                        <textarea name="deskripsi" class="form-control" rows="3" 
                                  placeholder="Contoh: Sudah dilakukan pergantian LCD baru oleh vendor Asus Center." required></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning font-weight-bold text-dark">
                        <i class="fas fa-check-circle"></i> Selesaikan Perawatan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endforeach