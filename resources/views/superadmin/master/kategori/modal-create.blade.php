<div class="modal fade" id="createKategoriModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg">

            {{-- HEADER --}}
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-tags mr-2"></i> Tambah Kategori
                </h5>
                <button class="close text-white" data-dismiss="modal">×</button>
            </div>

            <form action="{{ route('kategori.store') }}" method="POST">
                @csrf

                {{-- BODY --}}
                <div class="modal-body">

                    <small class="text-muted d-block mb-2">
                        Kode kategori akan dibuat otomatis oleh sistem
                    </small>

                    <div id="kategoriWrapper">
                        <div class="input-group mb-2 kategori-row">
                            <input type="text"
                                   name="nama_kategori[]"
                                   class="form-control"
                                   placeholder="Contoh: Elektronik"
                                   required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-danger btn-remove">×</button>
                            </div>
                        </div>
                    </div>

                    <button type="button"
                            class="btn btn-sm btn-outline-success mt-2"
                            id="addKategori">
                        + Tambah Baris
                    </button>

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

<script>
document.getElementById('addKategori').addEventListener('click', function () {
    const wrapper = document.getElementById('kategoriWrapper');

    const row = document.createElement('div');
    row.classList.add('input-group', 'mb-2', 'kategori-row');

    row.innerHTML = `
        <input type="text"
               name="nama_kategori[]"
               class="form-control"
               placeholder="Contoh: Elektronik"
               required>
        <div class="input-group-append">
            <button type="button" class="btn btn-danger btn-remove">×</button>
        </div>
    `;

    wrapper.appendChild(row);
    row.querySelector('input').focus();
});

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('btn-remove')) {
        const rows = document.querySelectorAll('.kategori-row');
        if (rows.length > 1) {
            e.target.closest('.kategori-row').remove();
        }
    }
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && e.target.closest('.kategori-row')) {
        e.preventDefault();
        document.getElementById('addKategori').click();
    }
});

const kategoriModal = document.getElementById('createKategoriModal');
const kategoriWrapper = document.getElementById('kategoriWrapper');

kategoriModal.addEventListener('hidden.bs.modal', function () {
    kategoriModal.querySelector('form').reset();

    kategoriWrapper.innerHTML = `
        <div class="input-group mb-2 kategori-row">
            <input type="text"
                   name="nama_kategori[]"
                   class="form-control"
                   placeholder="Contoh: Elektronik"
                   required>
            <div class="input-group-append">
                <button type="button" class="btn btn-danger btn-remove">×</button>
            </div>
        </div>
    `;
});
</script>
