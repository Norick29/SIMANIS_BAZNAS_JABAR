@foreach ($lokasis as $lokasi)
<div class="modal fade" id="deleteLokasiModal{{ $lokasi->id_lokasi }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Hapus Lokasi
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form action="{{ route('lokasi.destroy', $lokasi->id_lokasi) }}" method="POST">
                @csrf
                @method('DELETE')

                {{-- BODY --}}
                <div class="modal-body text-center">

                    <p class="mb-2">
                        Apakah Anda yakin ingin menghapus lokasi berikut?
                    </p>

                    <div class="alert alert-light border">
                        <strong>{{ $lokasi->kode_lokasi }}</strong><br>
                        {{ $lokasi->nama_lokasi }}
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
