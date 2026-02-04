<div class="modal fade" id="createJenisModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Tambah Jenis</h5>
                <button class="close text-white" data-dismiss="modal">×</button>
            </div>

            <form action="{{ route('jenis.store') }}" method="POST">
                @csrf

                <div class="modal-body">

                    <div id="jenisWrapper">
                        <div class="row mb-2 jenis-row">
                            <div class="col-md-3">
                                <input type="text"
                                       name="items[0][kode_jenis]"
                                       class="form-control"
                                       placeholder="Kode"
                                       required>
                            </div>

                            <div class="col-md-7">
                                <input type="text"
                                       name="items[0][nama_jenis]"
                                       class="form-control"
                                       placeholder="Nama Jenis"
                                       required>
                            </div>

                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger btn-remove">×</button>
                            </div>
                        </div>
                    </div>

                    <button type="button"
                            class="btn btn-sm btn-outline-success mt-2"
                            id="addJenis">
                        + Tambah Baris
                    </button>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button class="btn btn-success" type="submit">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
let jenisIndex = 1;

document.getElementById('addJenis').addEventListener('click', function () {
    const wrapper = document.getElementById('jenisWrapper');

    const row = document.createElement('div');
    row.classList.add('row', 'mb-2', 'jenis-row');

    row.innerHTML = `
        <div class="col-md-3">
            <input type="text"
                   name="items[${jenisIndex}][kode_jenis]"
                   class="form-control"
                   placeholder="Kode"
                   required>
        </div>

        <div class="col-md-7">
            <input type="text"
                   name="items[${jenisIndex}][nama_jenis]"
                   class="form-control"
                   placeholder="Nama Jenis"
                   required>
        </div>

        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-remove">×</button>
        </div>
    `;

    wrapper.appendChild(row);

    // 🔥 AUTO FOCUS ke kode jenis
    row.querySelector('input').focus();

    jenisIndex++;
});

// HAPUS BARIS (minimal 1)
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('btn-remove')) {
        const rows = document.querySelectorAll('.jenis-row');
        if (rows.length > 1) {
            e.target.closest('.jenis-row').remove();
        }
    }
});

// ENTER = TAMBAH BARIS BARU
document.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && e.target.closest('.jenis-row')) {
        e.preventDefault();
        document.getElementById('addJenis').click();
    }
});

// RESET SAAT MODAL DITUTUP
const jenisModal   = document.getElementById('createJenisModal');
const jenisWrapper = document.getElementById('jenisWrapper');

jenisModal.addEventListener('hidden.bs.modal', function () {

    jenisIndex = 1;

    jenisModal.querySelector('form').reset();

    jenisWrapper.innerHTML = `
        <div class="row mb-2 jenis-row">
            <div class="col-md-3">
                <input type="text"
                       name="items[0][kode_jenis]"
                       class="form-control"
                       placeholder="Kode"
                       required>
            </div>

            <div class="col-md-7">
                <input type="text"
                       name="items[0][nama_jenis]"
                       class="form-control"
                       placeholder="Nama Jenis"
                       required>
            </div>

            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-remove">×</button>
            </div>
        </div>
    `;
});
</script>
