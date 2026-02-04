<div class="modal fade" id="createDivisiModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
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

                    <label class="font-weight-bold">Daftar Divisi</label>

                    <div id="divisiWrapper">
                        <div class="input-group mb-2 divisi-row">
                            <input type="text"
                                   name="nama_divisi[]"
                                   class="form-control"
                                   placeholder="Contoh: Divisi Keuangan"
                                   required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-danger btn-remove">×</button>
                            </div>
                        </div>
                    </div>

                    <button type="button"
                            class="btn btn-sm btn-outline-success mt-2"
                            id="addDivisi">
                        + Tambah Baris
                    </button>

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

<script>
document.getElementById('addDivisi').addEventListener('click', function () {
    const wrapper = document.getElementById('divisiWrapper');

    const row = document.createElement('div');
    row.classList.add('input-group', 'mb-2', 'divisi-row');

    row.innerHTML = `
        <input type="text"
               name="nama_divisi[]"
               class="form-control"
               placeholder="Contoh: Divisi Keuangan"
               required>
        <div class="input-group-append">
            <button type="button" class="btn btn-danger btn-remove">×</button>
        </div>
    `;

    wrapper.appendChild(row);

    // 🔥 AUTO FOCUS
    row.querySelector('input').focus();
});

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('btn-remove')) {
        const rows = document.querySelectorAll('.divisi-row');
        if (rows.length > 1) {
            e.target.closest('.divisi-row').remove();
        }
    }
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && e.target.closest('.divisi-row')) {
        e.preventDefault();
        document.getElementById('addDivisi').click();
    }
});

const divisiModal   = document.getElementById('createDivisiModal');
const divisiWrapper = document.getElementById('divisiWrapper');

divisiModal.addEventListener('hidden.bs.modal', function () {

    // reset form
    divisiModal.querySelector('form').reset();

    // balikin ke 1 input awal
    divisiWrapper.innerHTML = `
        <div class="input-group mb-2 divisi-row">
            <input type="text"
                   name="nama_divisi[]"
                   class="form-control"
                   placeholder="Contoh: Divisi Keuangan"
                   required>
            <div class="input-group-append">
                <button type="button" class="btn btn-danger btn-remove">×</button>
            </div>
        </div>
    `;
});
</script>

