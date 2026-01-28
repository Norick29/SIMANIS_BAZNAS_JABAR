@foreach ($amil as $a)
<div class="modal fade" id="editAmilModal{{ $a->id_amil }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg">

            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-user-edit mr-2"></i> Edit Data Amil
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form action="{{ route('amil.update', $a->id_amil) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>NIA</label>
                                <input type="text" name="nia"
                                    class="form-control"
                                    value="{{ $a->nia }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Amil</label>
                                <input type="text" name="nama"
                                    class="form-control"
                                    value="{{ $a->nama }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jabatan</label>
                                <select name="id_jabatan" class="form-control" required>
                                    <option value="">-- Pilih Jabatan --</option>
                                    @foreach ($jabatan as $j)
                                        <option value="{{ $j->id_jabatan }}"
                                            {{ $a->id_jabatan == $j->id_jabatan ? 'selected' : '' }}>
                                            {{ $j->nama_jabatan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Divisi</label>
                                <select name="id_divisi" class="form-control" required>
                                    <option value="">-- Pilih Divisi --</option>
                                    @foreach ($divisi as $d)
                                        <option value="{{ $d->id_divisi }}"
                                            {{ $a->id_divisi == $d->id_divisi ? 'selected' : '' }}>
                                            {{ $d->nama_divisi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Departemen</label>
                                <input type="text" name="nama_dept"
                                    class="form-control"
                                    value="{{ $a->nama_dept }}">
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>
                    <button class="btn btn-warning">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endforeach
