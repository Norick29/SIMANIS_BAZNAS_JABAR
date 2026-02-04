<div class="modal fade" id="createLokasiModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg">

            {{-- HEADER --}}
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-tags mr-2"></i> Tambah Lokasi
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form action="{{ route('lokasi.store') }}" method="POST">
                @csrf

                {{-- BODY --}}
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Lokasi</label>
                        <input type="text"
                            class="form-control"
                            value="Auto Generate"
                            readonly>
                        <small class="text-muted">
                            Kode lokasi akan dibuat otomatis oleh sistem
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Nama Lokasi</label>
                        <input 
                            type="text"
                            name="nama_lokasi"
                            class="form-control @error('nama_lokasi') is-invalid @enderror"
                            placeholder="Contoh: Ruang Ketua"
                            value="{{ old('nama_lokasi') }}"
                            required
                        >
                        @error('nama_lokasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
