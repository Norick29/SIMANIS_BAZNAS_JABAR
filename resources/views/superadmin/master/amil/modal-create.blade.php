<!-- Create Amil Modal -->
<div class="modal fade" id="createAmilModal" tabindex="-1" role="dialog"
     aria-labelledby="createAmilLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="createAmilLabel">Tambah Amil</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>×</span>
                </button>
            </div>

            <form id="createAmilForm" action="{{ route('amil.store') }}" method="POST">
                @csrf

                <div class="modal-body">

                    {{-- NIA --}}
                    <div class="form-group">
                        <label>NIA</label>
                        <input type="text"
                               name="nia"
                               class="form-control @error('nia') is-invalid @enderror"
                               placeholder="Masukkan NIA"
                               required>
                        @error('nia')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nama --}}
                    <div class="form-group">
                        <label>Nama Amil</label>
                        <input type="text"
                               name="nama"
                               class="form-control @error('nama') is-invalid @enderror"
                               placeholder="Masukkan nama amil"
                               required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Jabatan --}}
                    <div class="form-group">
                        <label>Jabatan</label>
                        <select name="id_jabatan"
                                class="form-control @error('id_jabatan') is-invalid @enderror"
                                required>
                            <option value="" disabled selected>-- Pilih Jabatan --</option>
                            @foreach ($jabatan as $j)
                                <option value="{{ $j->id_jabatan }}">
                                    {{ $j->nama_jabatan }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_jabatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Divisi --}}
                    <div class="form-group">
                        <label>Divisi</label>
                        <select name="id_divisi"
                                class="form-control @error('id_divisi') is-invalid @enderror"
                                required>
                            <option value="" disabled selected>-- Pilih Divisi --</option>
                            @foreach ($divisi as $d)
                                <option value="{{ $d->id_divisi }}">
                                    {{ $d->nama_divisi }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_divisi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Nama Departemen</label>
                        <input type="text"
                               name="nama_dept"
                               class="form-control @error('nama_dept') is-invalid @enderror"
                               placeholder="Masukkan nama departemen"
                               required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
