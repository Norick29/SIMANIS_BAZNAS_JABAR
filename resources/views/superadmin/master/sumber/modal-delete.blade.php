@foreach ($sumber as $k)
<div class="modal fade" id="deleteSumberModal{{ $k->id_sumber_dana }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Hapus Sumber Dana
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form action="{{ route('sumber.destroy', $k->id_sumber_dana) }}" method="POST">
                @csrf
                @method('DELETE')

                {{-- BODY --}}
                <div class="modal-body text-center">

                    <p class="mb-2">
                        Apakah Anda yakin ingin menghapus sumber dana berikut?
                    </p>

                    <div class="alert alert-light border">
                        <strong>{{ $k->kode_sumber_dana }}</strong><br>
                        {{ $k->nama_sumber_dana }}
                    </div>

                    <p class="text-danger mb-0">
                        <small>Data yang dihapus tidak dapat dikembalikan.</small>
                    </p>

                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>

                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endforeach
