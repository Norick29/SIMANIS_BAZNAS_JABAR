@foreach ($maintenances as $m)
<div class="modal fade" id="deleteMaintenanceModal{{ $m->id_maintenance }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Pembatalan Log Perawatan
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form action="{{ route('maintenances.destroy', $m->id_maintenance) }}" method="POST">
                @csrf
                @method('DELETE')

                <div class="modal-body text-center">
                    <p class="mb-2">Apakah Anda yakin ingin menghapus catatan maintenance untuk aset berikut?</p>
                    
                    <h5 class="font-weight-bold text-danger">
                        {{ $m->asset->barang->nama_barang ?? '-' }}
                    </h5>
                    
                    <small class="text-muted d-block mb-3">
                        Kode: {{ $m->asset->kode_aset ?? '-' }}
                    </small>

                    <div class="alert alert-warning">
                        Tindakan ini akan menghapus log histori perawatan dari sistem secara logis (*soft delete*).
                    </div>
                </div>

                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Ya, Hapus Log</button>
                </div>
            </form>

        </div>
    </div>
</div>
@endforeach