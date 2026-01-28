@foreach ($divisi as $d)
<div class="modal fade" id="deleteDivisiModal{{ $d->id_divisi }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">

            {{-- HEADER --}}
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Konfirmasi Hapus Divisi
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            {{-- FORM --}}
            <form action="{{ route('divisi.destroy', $d->id_divisi) }}" method="POST">
                @csrf
                @method('DELETE')

                {{-- BODY --}}
                <div class="modal-body text-center">
                    <p class="mb-2">
                        Apakah Anda yakin ingin menghapus divisi berikut?
                    </p>

                    <h5 class="font-weight-bold text-danger">
                        {{ $d->nama_divisi }}
                    </h5>

                    <div class="alert alert-warning mt-3">
                        Divisi yang dihapus <b>tidak dapat dikembalikan</b>
                        dan dapat mempengaruhi data amil yang terkait.
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button class="btn btn-danger">
                        <i class="fas fa-trash"></i> Ya, Hapus
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endforeach
