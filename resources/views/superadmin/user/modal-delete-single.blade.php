<div class="modal fade" id="deleteUserModal" tabindex="-1">
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
            <form id="deleteUserForm" action="" method="POST">
                @csrf
                @method('DELETE')

                {{-- BODY --}}
                <div class="modal-body text-center">
                    <p class="mb-2">
                        Apakah Anda yakin ingin menghapus data user berikut?
                    </p>

                    <h5 class="font-weight-bold text-danger">
                        <span id="delete_user_name"></span>
                    </h5>

                    <small class="text-muted">
                        Email: <span id="delete_user_email"></span><br>
                        Role: <span id="delete_user_role"></span>
                    </small>

                    <div class="alert alert-warning mt-3">
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