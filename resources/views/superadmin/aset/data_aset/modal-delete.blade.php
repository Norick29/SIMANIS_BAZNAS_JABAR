<div class="modal fade" id="deleteAssetModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">

            {{-- HEADER --}}
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Konfirmasi Hapus
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            {{-- FORM --}}
            <form id="deleteAssetForm" method="POST">
                @csrf
                @method('DELETE')

                {{-- BODY --}}
                <div class="modal-body text-center">
                    <p class="mb-3">
                        Apakah Anda yakin ingin menghapus aset berikut?
                    </p>

                    <h5 class="font-weight-bold text-danger" id="delete_kode_aset">
                        -
                    </h5>

                    <small class="text-muted" id="delete_barang">
                        -
                    </small>

                    <div class="alert alert-warning mt-3 mb-0">
                        Data yang sudah dihapus <b>tidak dapat dikembalikan</b>.
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Ya, Hapus
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Handle Delete Modal - Set form action & data
    document.querySelectorAll('[data-target="#deleteAssetModal"]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = btn.getAttribute('data-id');
            
            fetch(`/assets/${id}`)
                .then(response => response.json())
                .then(data => {
                    // Set form action
                    document.getElementById('deleteAssetForm').action = `/assets/${id}`;
                    
                    // Populate modal dengan data
                    document.getElementById('delete_kode_aset').textContent = data.kode_aset || '-';
                    document.getElementById('delete_barang').textContent = data.barang || '-';
                    
                    // Trigger modal
                    $('#deleteAssetModal').modal('show');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal memuat data aset');
                });
        });
    });

});
</script>