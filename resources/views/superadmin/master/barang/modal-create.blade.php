<div class="modal fade" id="createBarangModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg">

            {{-- HEADER --}}
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-tags mr-2"></i> Tambah Barang
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form action="{{ route('barang.store') }}" method="POST">
                @csrf

                {{-- BODY --}}
                <div class="modal-body">
                    <div class="form-group">
                        <label>Kode Barang</label>
                        <input type="text"
                            class="form-control"
                            value="Auto Generate"
                            readonly>
                        <small class="text-muted">
                            Kode barang akan dibuat otomatis oleh sistem
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input 
                            type="text"
                            name="nama_barang"
                            class="form-control @error('nama_barang') is-invalid @enderror"
                            placeholder="Contoh: Laptop"
                            value="{{ old('nama_barang') }}"
                            required
                        >
                        @error('nama_barang')
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
