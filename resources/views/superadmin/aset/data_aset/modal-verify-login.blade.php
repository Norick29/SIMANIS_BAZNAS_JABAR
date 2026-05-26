<div class="modal fade" id="verifyLoginModal" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-lock mr-2"></i> Verifikasi Identitas
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form id="verifyLoginForm">
                @csrf

                <div class="modal-body">
                    <p class="mb-3 text-muted">
                        Untuk keamanan, silakan verifikasi identitas Anda dengan memasukkan password.
                    </p>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" id="verify_password" class="form-control" required>
                        <small class="form-text text-muted">
                            Password Anda akan hanya digunakan untuk verifikasi dan tidak disimpan.
                        </small>
                    </div>

                    <div id="verifyError" class="alert alert-danger" style="display:none;"></div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-check"></i> Verifikasi
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
document.getElementById('verifyLoginForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const password = document.getElementById('verify_password').value;
    const assetId = "{{ $aset->id_aset }}";
    const errorDiv = document.getElementById('verifyError');

    fetch('{{ route("assets.verify-login") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify({
            password: password,
            id_aset: assetId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Tutup modal verifikasi
            $('#verifyLoginModal').modal('hide');
            
            // Buka modal form pindah
            $('#pindahFormModal').modal('show');
            
            // Reset form
            document.getElementById('verifyLoginForm').reset();
            errorDiv.style.display = 'none';
        } else {
            errorDiv.style.display = 'block';
            errorDiv.textContent = data.message || 'Verifikasi gagal';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        errorDiv.style.display = 'block';
        errorDiv.textContent = 'Terjadi kesalahan saat verifikasi';
    });
});
</script>