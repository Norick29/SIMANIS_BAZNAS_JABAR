<div class="modal fade" id="createJabatanModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Tambah Jabatan</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form action="{{ route('jabatan.store') }}" method="POST">
                @csrf

                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Jabatan</label>
                        <input 
                            type="text" 
                            name="nama_jabatan" 
                            class="form-control @error('nama_jabatan') is-invalid @enderror"
                            placeholder="Contoh: Kepala Divisi"
                            required
                        >
                        @error('nama_jabatan')
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
