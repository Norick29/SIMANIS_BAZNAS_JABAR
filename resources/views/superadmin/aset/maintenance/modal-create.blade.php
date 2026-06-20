<div class="modal fade" id="createMaintenanceModal" tabindex="-1" role="dialog"
     aria-labelledby="createMaintenanceLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="createMaintenanceLabel"><i class="fas fa-tools mr-2"></i>Tambah Jadwal Perawatan</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form id="createMaintenanceForm" action="{{ route('maintenances.store') }}" method="POST">
                @csrf

                <div class="modal-body">

                    {{-- PILIH ASET RUSAK --}}
                    <div class="form-group">
                        <label>Aset yang Membutuhkan Perawatan</label>
                        <select name="id_aset" class="form-control @error('id_aset') is-invalid @enderror" required>
                            <option value="" disabled selected>-- Pilih Aset Rusak --</option>
                            @foreach ($assetsReadyToMaintain as $asset)
                                <option value="{{ $asset->id_aset }}">
                                    {{ $asset->kode_aset }} - {{ $asset->barang->nama_barang ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_aset')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- JENIS PERAWATAN --}}
                    <div class="form-group">
                        <label>Jenis Perawatan</label>
                        <select name="jenis" class="form-control @error('jenis') is-invalid @enderror" required>
                            <option value="" disabled selected>-- Pilih Jenis --</option>
                            <option value="perbaikan">Perbaikan (Repair)</option>
                            <option value="servis">Servis Berkala (Maintenance)</option>
                            <option value="kalibrasi">Kalibrasi</option>
                        </select>
                        @error('jenis')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- BIAYA --}}
                    <div class="form-group">
                        <label>Estimasi Biaya (Rp)</label>
                        <input type="number" name="biaya" class="form-control @error('biaya') is-invalid @enderror" 
                               placeholder="Masukkan nominal biaya" min="0" required>
                        @error('biaya')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- TANGGAL MULAI --}}
                    <div class="form-group">
                        <label>Tanggal Mulai Perawatan</label>
                        <input type="date" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" required>
                        @error('tanggal_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- DESKRIPSI MASALAH --}}
                    <div class="form-group">
                        <label>Deskripsi Kerusakan / Catatan</label>
                        <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" 
                                  rows="3" placeholder="Jelaskan detail tindakan perawatan atau gejala kerusakan barang" required></textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Kirim ke Perawatan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>