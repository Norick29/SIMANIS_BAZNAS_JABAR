<div class="modal fade"
     id="editUserModal"
     tabindex="-1"
     role="dialog"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg">

            {{-- HEADER --}}
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-user-edit mr-2"></i> Edit User
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            {{-- FORM --}}
            <form id="editUserForm" action="" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_user_id" name="user_id">

                {{-- BODY --}}
                <div class="modal-body">
                    <div class="row">

                        {{-- NAMA --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Full Name</strong></label>
                                <input type="text"
                                       id="edit_name"
                                       name="name"
                                       class="form-control"
                                       required>
                            </div>
                        </div>

                        {{-- EMAIL --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Email</strong></label>
                                <input type="email"
                                       id="edit_email"
                                       name="email"
                                       class="form-control"
                                       required>
                            </div>
                        </div>

                        {{-- NO HP --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Phone Number</strong></label>
                                <input type="text"
                                       id="edit_no_hp"
                                       name="no_hp"
                                       class="form-control"
                                       required>
                            </div>
                        </div>

                        {{-- ROLE --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Role</strong></label>
                                <select id="edit_role" name="role" class="form-control" required>
                                    <option value="super admin">Super Admin</option>
                                    <option value="kadiv">Kepala Divisi</option>
                                    <option value="kadept">Kepala Departement</option>
                                    <option value="staf">Staf</option>
                                </select>
                            </div>
                        </div>

                        {{-- PASSWORD --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Password (Kosongkan jika tidak ingin mengubah)</strong></label>
                                <input type="password"
                                       id="edit_password"
                                       name="password"
                                       class="form-control"
                                       placeholder="Masukkan password baru (opsional)">
                            </div>
                        </div>

                        {{-- PASSWORD CONFIRMATION --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Confirm Password</strong></label>
                                <input type="password"
                                       id="edit_password_confirmation"
                                       name="password_confirmation"
                                       class="form-control"
                                       placeholder="Konfirmasi password (opsional)">
                            </div>
                        </div>

                        {{-- RSA KEY STATUS --}}
                        <div class="col-md-12">
                            <div id="rsaInfo" class="alert alert-info" style="display: none;">
                                <i class="fas fa-info-circle"></i>
                                <strong>RSA Digital Signature:</strong> User ini memiliki kunci RSA untuk digital signature.
                                
                                <a id="downloadKeyBtn" href="" 
                                   class="btn btn-sm btn-success ml-2"
                                   style="display: none;">
                                    <i class="fas fa-download"></i> Download Private Key
                                </a>
                            </div>

                            <div id="rsaWarning" class="alert alert-warning" style="display: none;">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>RSA Digital Signature:</strong> User dengan role <span id="rsaRoleText"></span> tidak memiliki kunci RSA.
                            </div>
                        </div>

                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>

                    <button type="submit"
                            class="btn btn-warning">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>