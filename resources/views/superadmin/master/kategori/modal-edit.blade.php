@foreach ($kategori as $k)
<div class="modal fade"
     id="editKategoriModal{{ $k->id_kategori }}"
     tabindex="-1"
     role="dialog">

    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg">

            {{-- HEADER --}}
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-edit mr-2"></i> Edit Kategori
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form action="{{ route('kategori.update', $k->id_kategori) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- BODY --}}
                <div class="modal-body">

                    <div class="form-group">
                        <label>Kode Kategori</label>
                        <input type="text"
                               class="form-control"
                               value="{{ $k->kode_kategori }}"
                               readonly>
                        <small class="text-muted">
                            Kode kategori tidak dapat diubah
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text"
                               name="nama_kategori"
                               class="form-control @error('nama_kategori') is-invalid @enderror"
                               value="{{ $k->nama_kategori }}"
                               required>
                        @error('nama_kategori')
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
