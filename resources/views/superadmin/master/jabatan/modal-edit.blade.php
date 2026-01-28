@foreach ($jabatan as $j)
<div class="modal fade" id="editJabatanModal{{ $j->id_jabatan }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">

            {{-- HEADER --}}
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-briefcase mr-2"></i>
                    Edit Jabatan
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            {{-- FORM --}}
            <form action="{{ route('jabatan.update', $j->id_jabatan) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- BODY --}}
                <div class="modal-body">

                    <div class="form-group">
                        <label>Nama Jabatan</label>
                        <input type="text"
                            name="nama_jabatan"
                            class="form-control"
                            value="{{ $j->nama_jabatan }}"
                            required>
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                    <button class="btn btn-warning">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endforeach
