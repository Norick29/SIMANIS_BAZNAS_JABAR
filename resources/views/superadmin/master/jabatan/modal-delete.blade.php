@foreach ($jabatan as $j)
<div class="modal fade" id="deleteJabatanModal{{ $j->id_jabatan }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">

            {{-- HEADER --}}
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Konfirmasi Hapus Jabatan
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            {{-- FORM --}}
            <form action="{{ route('jabatan.destroy', $j->id_jabatan) }}" method="POST">
                @csrf
                @method('DELETE')

                {{-- BODY --}}
                <div class="modal-body text-center">
                    <p class="mb-2">
                        Apakah Anda yakin ingin menghapus jabatan berikut?
                    </p>

                    <h5 class="font-weight-bold text-danger">
                        {{ $j->nama_jabatan }}
                    </h5>

                    <div class="alert alert-warning mt-3">
                        Jabatan yang dihapus <b>tidak dapat dikembalikan</b>
                        dan dapat mempengaruhi data amil terkait.
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
