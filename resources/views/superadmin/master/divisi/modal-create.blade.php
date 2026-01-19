<div class="modal fade" id="createDivisiModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Tambah Divisi</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form action="{{ route('divisi.store') }}" method="POST">
                @csrf

                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Divisi</label>
                        <input 
                            type="text" 
                            name="nama_divisi" 
                            class="form-control @error('nama_divisi') is-invalid @enderror"
                            placeholder="Contoh: Divisi Keuangan"
                            required
                        >
                        @error('nama_divisi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
