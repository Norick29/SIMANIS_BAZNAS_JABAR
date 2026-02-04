<div class="modal fade" id="importJenisModal" tabindex="-1">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <form action="{{ route('jenis.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content shadow-lg">
                
                {{-- HEADER --}}
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-file-import mr-2"></i> Import Data Jenis
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
                                   id="fileImportJenis"
                                   name="file"
                                   accept=".xls,.xlsx"
                                   required>
                            <label class="custom-file-label" for="fileImportJenis">
                                Pilih file (.xls / .xlsx)
                            </label>
                        </div>

                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-info-circle"></i>
                            Format kolom: <b>nama </b>
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
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.querySelector('.custom-file-input');

    fileInput.addEventListener('change', function (e) {
        const fileName = e.target.files[0]?.name || 'Pilih file...';
        e.target.nextElementSibling.innerText = fileName;
    });
});
</script>


                                   
