@foreach ($sumber as $k)
<div class="modal fade"
     id="editSumberModal{{ $k->id_sumber_dana }}"
     tabindex="-1"
     role="dialog">

    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg">

            {{-- HEADER --}}
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-edit mr-2"></i> Edit Sumber Dana
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form action="{{ route('sumber.update', $k->id_sumber_dana) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- BODY --}}
                <div class="modal-body">

                    <div class="form-group">
                        <label>Kode Sumber Dana</label>
                        <input type="text"
                               class="form-control"
                               value="{{ $k->kode_sumber_dana }}"
                               readonly>
                        <small class="text-muted">
                            Kode sumber dana tidak dapat diubah
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Nama Sumber Dana</label>
                        <input type="text"
                               name="nama_sumber_dana"
                               class="form-control @error('nama_sumber_dana') is-invalid @enderror"
                               value="{{ $k->nama_sumber_dana }}"
                               required>
                        @error('nama_sumber_dana')
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
