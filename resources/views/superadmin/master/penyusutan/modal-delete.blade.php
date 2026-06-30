@foreach($masterPenyusutan as $item)

<div class="modal fade"
     id="deleteMasterPenyusutanModal{{ $item->id_master_penyusutan }}"
     tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header bg-danger text-white">

                <h5 class="modal-title">

                    <i class="fas fa-trash mr-2"></i>

                    Hapus Kebijakan Penyusutan

                </h5>

                <button
                    type="button"
                    class="close text-white"
                    data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <form action="{{ route('master-penyusutan.destroy',$item->id_master_penyusutan) }}"
                  method="POST">

                @csrf
                @method('DELETE')

                <div class="modal-body text-center">

                    <p>

                        Apakah Anda yakin ingin menghapus
                        kebijakan penyusutan berikut?

                    </p>

                    <div class="alert alert-light border">

                        <strong>

                            {{ $item->jenis->nama_jenis }}

                        </strong>

                        <br>

                        Umur Ekonomis :

                        {{ $item->umur_ekonomis }}

                        Tahun

                        <br>

                        Penyusutan :

                        {{ $item->persentase }} %

                    </div>

                    <small class="text-danger">

                        Data yang dihapus tidak dapat dikembalikan.

                    </small>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">

                        <i class="fas fa-times"></i>

                        Batal

                    </button>

                    <button
                        type="submit"
                        class="btn btn-danger">

                        <i class="fas fa-trash"></i>

                        Hapus

                    </button>

                </div>

            </form>

        </div>
    </div>

</div>

@endforeach