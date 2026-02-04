@foreach ($jenis as $jenis)
<div class="modal fade"
     id="editJenisModal{{ $jenis->id_jenis }}"
     tabindex="-1"
     role="dialog">

    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg">

            {{-- HEADER --}}
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-edit mr-2"></i> Edit Jenis
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form action="{{ route('jenis.update', $jenis->id_jenis) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- BODY --}}
                <div class="modal-body">

                    <div class="form-group">
                        <label>Kode Jenis</label>
                        <input type="text"
                               class="form-control"
                               value="{{ $jenis->kode_jenis }}"
                               readonly>
                        <small class="text-muted">
                            Kode jenis tidak dapat diubah
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Nama Jenis</label>
                        <input type="text"
                               name="nama_jenis"
                               class="form-control @error('nama_jenis') is-invalid @enderror"
                               value="{{ $jenis->nama_jenis }}"
                               required>
                        @error('nama_jenis')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-warning">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endforeach
