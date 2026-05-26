<div class="modal fade"
     id="pindahFormModal"
     tabindex="-1"
     data-backdrop="static"
     data-keyboard="false">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-exchange-alt mr-2"></i>
                    Form Perpindahan Aset
                </h5>

                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form id="pindahAsetForm">

                @csrf

                <input type="hidden"
                       name="id_aset"
                       id="pindah_id_aset">

                <div class="modal-body">

                    {{-- INFO ASET --}}
                    <div class="alert alert-info">

                        <h6>
                            <strong>Aset yang akan dipindahkan:</strong>
                        </h6>

                        <small>
                            <strong>Kode Aset:</strong>
                            <span id="p_kode_aset"></span>
                            <br>

                            <strong>Barang:</strong>
                            <span id="p_barang"></span>
                            <br>

                            <strong>Lokasi Saat Ini:</strong>
                            <span id="p_lokasi"></span>
                            <br>

                            <strong>Penanggung Jawab:</strong>
                            <span id="p_amil"></span>
                        </small>

                    </div>

                    <hr>

                    {{-- PENANGGUNG JAWAB BARU --}}
                    <div class="form-group">

                        <label>
                            <strong>Penanggung Jawab Baru</strong>
                        </label>

                        <select name="id_amil_sesudah"
                                id="id_amil_sesudah"
                                class="form-control select-amil-pindah"
                                required>

                            <option value="">
                                -- Pilih Penanggung Jawab --
                            </option>

                            @foreach ($amils as $a)
                                <option value="{{ $a->id_amil }}">
                                    {{ $a->nia }} - {{ $a->nama }}
                                </option>
                            @endforeach

                        </select>

                    </div>

                    {{-- LOKASI BARU --}}
                    <div class="form-group">

                        <label>
                            <strong>Lokasi Baru</strong>
                        </label>

                        <select name="id_lokasi_baru"
                                id="id_lokasi_baru"
                                class="form-control select-lokasi-pindah"
                                required>

                            <option value="">
                                -- Pilih Lokasi --
                            </option>

                            @foreach ($lokasis as $l)
                                <option value="{{ $l->id_lokasi }}">
                                    {{ $l->kode_lokasi }} - {{ $l->nama_lokasi }}
                                </option>
                            @endforeach

                        </select>

                    </div>

                    {{-- ALASAN --}}
                    <div class="form-group">

                        <label>
                            <strong>Alasan Perpindahan</strong>
                        </label>

                        <textarea name="alasan"
                                  class="form-control"
                                  rows="4"
                                  required></textarea>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-warning">

                        <i class="fas fa-paper-plane"></i>
                        Kirim Permintaan

                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

<script>

document.addEventListener('DOMContentLoaded', function () {

    // INIT SELECT2
$('#pindahFormModal').on('shown.bs.modal', function () {

    // SELECT2 AMIL
    if (!$('.select-amil-pindah').hasClass('select2-hidden-accessible')) {

        $('.select-amil-pindah').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Cari penanggung jawab...',
            dropdownParent: $('#pindahFormModal')
        });

    }

    // SELECT2 LOKASI
    if (!$('.select-lokasi-pindah').hasClass('select2-hidden-accessible')) {

        $('.select-lokasi-pindah').select2({
            theme: 'bootstrap4',
            width: '100%',
            placeholder: 'Cari lokasi...',
            dropdownParent: $('#pindahFormModal')
        });

    }

});

    // SUBMIT FORM
    document.getElementById('pindahAsetForm')
        .addEventListener('submit', function (e) {

        e.preventDefault();

        const formData = new FormData(this);

        fetch('{{ route("assets.pindah") }}', {

            method: 'POST',

            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },

            body: formData

        })

        .then(response => response.json())

        .then(data => {

            if (data.success) {

                alert(data.message);

                $('#pindahFormModal').modal('hide');

                location.reload();

            } else {

                alert(data.message);

            }

        })

        .catch(error => {

            console.error(error);

            alert('Terjadi kesalahan');

        });

    });

});

</script>