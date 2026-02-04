<div class="modal fade" id="createJabatanModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
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

                    <label class="font-weight-bold">Daftar Jabatan</label>

                    <div id="jabatanWrapper">
                        <div class="input-group mb-2 jabatan-row">
                            <input type="text"
                                   name="nama_jabatan[]"
                                   class="form-control"
                                   placeholder="Contoh: Kepala Divisi"
                                   required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-danger btn-remove">
                                    ×
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="button"
                            class="btn btn-sm btn-outline-success mt-2"
                            id="addJabatan">
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
document.getElementById('addJabatan').addEventListener('click', function () {
    const wrapper = document.getElementById('jabatanWrapper');

    const row = document.createElement('div');
    row.classList.add('input-group', 'mb-2', 'jabatan-row');

    row.innerHTML = `
        <input type="text"
               name="nama_jabatan[]"
               class="form-control"
               placeholder="Contoh: Kepala Divisi"
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
        const rows = document.querySelectorAll('.jabatan-row');
        if (rows.length > 1) {
            e.target.closest('.jabatan-row').remove();
        }
    }
});

document.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && e.target.closest('.jabatan-row')) {
        e.preventDefault();
        document.getElementById('addJabatan').click();
    }
});

const jabatanModal   = document.getElementById('createJabatanModal');
const jabatanWrapper = document.getElementById('jabatanWrapper');

jabatanModal.addEventListener('hidden.bs.modal', function () {

    // reset form
    jabatanModal.querySelector('form').reset();

    // kembalikan ke 1 input awal
    jabatanWrapper.innerHTML = `
        <div class="input-group mb-2 jabatan-row">
            <input type="text"
                   name="nama_jabatan[]"
                   class="form-control"
                   placeholder="Contoh: Kepala Divisi"
                   required>
            <div class="input-group-append">
                <button type="button" class="btn btn-danger btn-remove">×</button>
            </div>
        </div>
    `;
});
</script>

