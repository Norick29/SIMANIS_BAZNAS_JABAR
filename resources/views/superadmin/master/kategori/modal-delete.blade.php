@foreach ($kategori as $k)
<div class="modal fade" id="deleteKategoriModal{{ $k->id_kategori }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Hapus Kategori
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form action="{{ route('kategori.destroy', $k->id_kategori) }}" method="POST">
                @csrf
                @method('DELETE')

                {{-- BODY --}}
                <div class="modal-body text-center">

                    <p class="mb-2">
                        Apakah Anda yakin ingin menghapus kategori berikut?
                    </p>

                    <div class="alert alert-light border">
                        <strong>{{ $k->kode_kategori }}</strong><br>
                        {{ $k->nama_kategori }}
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
