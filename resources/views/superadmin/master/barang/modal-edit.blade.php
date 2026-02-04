@foreach ($barangs as $barang)
<div class="modal fade"
     id="editBarangModal{{ $barang->id_barang }}"
     tabindex="-1"
     role="dialog">

    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg">

            {{-- HEADER --}}
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-edit mr-2"></i> Edit Barang
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form action="{{ route('barang.update', $barang->id_barang) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- BODY --}}
                <div class="modal-body">

                    <div class="form-group">
                        <label>Kode Barang</label>
                        <input type="text"
                               class="form-control"
                               value="{{ $barang->kode_barang }}"
                               readonly>
                        <small class="text-muted">
                            Kode barang tidak dapat diubah
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text"
                               name="nama_barang"
                               class="form-control @error('nama_barang') is-invalid @enderror"
                               value="{{ $barang->nama_barang }}"
                               required>
                        @error('nama_barang')
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
