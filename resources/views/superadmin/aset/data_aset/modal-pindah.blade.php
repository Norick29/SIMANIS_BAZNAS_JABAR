<div class="modal fade" id="pindahAsetModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form id="pindahAsetForm" method="POST" action="{{ route('assets.pindah') }}">
                @csrf

                <input type="hidden" name="id_aset" id="pindah_id_aset">


                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Pindah Aset</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    {{-- <div class="col-md-6 mb-2"> --}}
                    <div class="form-group">
                        <label>Penanggung Jawab Baru</label>
                        <select name="id_amil_sesudah" class="form-control select-amil" required>
                            <option value="">-- Pilih Amil --</option>
                            @foreach ($amils as $a)
                                <option value="{{ $a->id_amil }}">{{ $a->nia }} - {{ $a->nama }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="form-group">
                        <label>Lokasi Baru</label>
                        <select name="id_lokasi_baru" class="form-control select-lokasi" required>
                            <option value="">-- Pilih Lokasi --</option>
                            @foreach ($lokasis as $l)
                                <option value="{{ $l->id_lokasi }}">
                                    {{ $l->kode_lokasi }} - {{ $l->nama_lokasi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Alasan Pindah</label>
                        <textarea name="alasan" class="form-control" rows="3"
                            placeholder="Contoh: Dipindahkan ke divisi lain"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-warning">Simpan</button>
                    <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

    $('#pindahAsetModal').on('shown.bs.modal', function () {

            $('.select-lokasi').select2({
                theme: 'bootstrap4',
                placeholder: 'Cari lokasi...',
                width: '100%',
                dropdownParent: $('#pindahAsetModal')
            });

            $('.select-amil').select2({
                theme: 'bootstrap4',
                placeholder: 'Cari penanggung jawab...',
                width: '100%',
                dropdownParent: $('#pindahAsetModal')
            });

        });

    });
</script>