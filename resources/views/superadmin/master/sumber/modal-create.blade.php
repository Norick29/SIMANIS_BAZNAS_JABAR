<div class="modal fade" id="createSumberDanaModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Tambah Sumber Dana</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    ×
                </button>
            </div>

            <form action="{{ route('sumber.store') }}" method="POST">
                @csrf

                <div class="modal-body">

                    <div id="sumberWrapper">
                        <div class="input-group mb-2 sumber-row">
                            <input type="text"
                                   name="nama_sumber_dana[]"
                                   class="form-control"
                                   placeholder="Contoh: Dana Zakat"
                                   required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-danger btn-remove">×</button>
                            </div>
                        </div>
                    </div>

                    <button type="button"
                            class="btn btn-sm btn-outline-success mt-2"
                            id="addSumber">
                        + Tambah Baris
                    </button>

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
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
document.getElementById('addSumber').addEventListener('click', function () {
    const wrapper = document.getElementById('sumberWrapper');

    const row = document.createElement('div');
    row.classList.add('input-group', 'mb-2', 'sumber-row');

    row.innerHTML = `
        <input type="text"
               name="nama_sumber_dana[]"
               class="form-control"
               placeholder="Contoh: Dana Zakat"
               required>
        <div class="input-group-append">
            <button type="button" class="btn btn-danger btn-remove">×</button>
        </div>
    `;

    wrapper.appendChild(row);

    // 🔥 AUTO FOCUS
    row.querySelector('input').focus();
});

// hapus baris
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('btn-remove')) {
        const rows = document.querySelectorAll('.sumber-row');
        if (rows.length > 1) {
            e.target.closest('.sumber-row').remove();
        }
    }
});

// tekan Enter = tambah baris
document.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && e.target.closest('.sumber-row')) {
        e.preventDefault();
        document.getElementById('addSumber').click();
    }
});

// reset saat modal ditutup
const sumberModal   = document.getElementById('createSumberDanaModal');
const sumberWrapper = document.getElementById('sumberWrapper');

sumberModal.addEventListener('hidden.bs.modal', function () {

    // reset form
    sumberModal.querySelector('form').reset();

    // kembalikan ke 1 input awal
    sumberWrapper.innerHTML = `
        <div class="input-group mb-2 sumber-row">
            <input type="text"
                   name="nama_sumber_dana[]"
                   class="form-control"
                   placeholder="Contoh: Dana Zakat"
                   required>
            <div class="input-group-append">
                <button type="button" class="btn btn-danger btn-remove">×</button>
            </div>
        </div>
    `;
});
</script>
