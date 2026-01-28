{{-- <div class="modal fade" id="importAmilModal">
    <div class="modal-dialog">
        <form action="{{ route('amil.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Import Data Amil</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="file" name="file" class="form-control" required
                           accept=".xls,.xlsx">
                    <small class="text-muted mt-2 d-block">
                        Format: nia | nama | jabatan | divisi
                    </small>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button class="btn btn-primary">
                        <i class="fas fa-upload"></i> Import
                    </button>
                </div>
            </div>
        </form>
    </div>
</div> --}}
<div class="modal fade" id="importAmilModal" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <form action="{{ route('amil.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content shadow-lg">
                
                {{-- HEADER --}}
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-file-import mr-2"></i> Import Data Amil
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                {{-- BODY --}}
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">
                            Pilih File Excel
                        </label>

                        <div class="custom-file">
                            <input type="file"
                                   class="custom-file-input"
                                   id="fileImportAmil"
                                   name="file"
                                   accept=".xls,.xlsx"
                                   required>
                            <label class="custom-file-label" for="fileImportAmil">
                                Pilih file (.xls / .xlsx)
                            </label>
                        </div>

                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-info-circle"></i>
                            Format kolom: <b>nia | nama | jabatan | divisi | departemen</b>
                        </small>
                    </div>

                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="fas fa-exclamation-triangle"></i>
                        Pastikan data tidak duplikat dan sesuai format.
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload"></i> Import Data
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
    $('.custom-file-input').on('change', function () {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>
