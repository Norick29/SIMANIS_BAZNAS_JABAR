@foreach ($divisi as $d)
<div class="modal fade" id="editDivisiModal{{ $d->id_divisi }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">

            {{-- HEADER --}}
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-sitemap mr-2"></i>
                    Edit Divisi
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            {{-- FORM --}}
            <form action="{{ route('divisi.update', $d->id_divisi) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- BODY --}}
                <div class="modal-body">

                    <div class="form-group">
                        <label>Nama Divisi</label>
                        <input type="text"
                            name="nama_divisi"
                            class="form-control"
                            value="{{ $d->nama_divisi }}"
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
