@foreach ($lokasis as $lokasi)
<div class="modal fade"
     id="editLokasiModal{{ $lokasi->id_lokasi }}"
     tabindex="-1"
     role="dialog">

    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg">

            {{-- HEADER --}}
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-edit mr-2"></i> Edit Lokasi
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form action="{{ route('lokasi.update', $lokasi->id_lokasi) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- BODY --}}
                <div class="modal-body">

                    <div class="form-group">
                        <label>Kode Lokasi</label>
                        <input type="text"
                               class="form-control"
                               value="{{ $lokasi->kode_lokasi }}"
                               readonly>
                        <small class="text-muted">
                            Kode lokasi tidak dapat diubah
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Nama Lokasi</label>
                        <input type="text"
                               name="nama_lokasi"
                               class="form-control @error('nama_lokasi') is-invalid @enderror"
                               value="{{ $lokasi->nama_lokasi }}"
                               required>
                        @error('nama_lokasi')
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
