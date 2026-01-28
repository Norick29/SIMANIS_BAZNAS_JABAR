<div class="modal fade" id="createKategoriModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('kategori.store') }}" method="POST">
            @csrf

            <div class="modal-content shadow-lg">

                {{-- HEADER --}}
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-tags mr-2"></i> Tambah Kategori
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                {{-- BODY --}}
                <div class="modal-body">

                    <div class="form-group">
                        <label>Kode Kategori</label>
                        <input type="text"
                               name="kode_kategori"
                               class="form-control"
                               placeholder="Contoh: KAT-001"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text"
                               name="nama_kategori"
                               class="form-control"
                               placeholder="Masukkan nama kategori"
                               required>
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>

                    <button type="submit"
                            class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
