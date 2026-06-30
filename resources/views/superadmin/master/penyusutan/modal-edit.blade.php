@foreach($masterPenyusutan as $item)

<div class="modal fade"
     id="editMasterPenyusutanModal{{ $item->id_master_penyusutan }}"
     tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg">

            {{-- HEADER --}}
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Kebijakan Penyusutan
                </h5>

                <button type="button"
                        class="close"
                        data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>

            <form action="{{ route('master-penyusutan.update',$item->id_master_penyusutan) }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="modal-body">

                    <div class="form-group">

                        <label>Jenis Aset</label>

                        <select
                            name="id_jenis"
                            class="form-control"
                            required>

                            @foreach($jenis as $j)

                                <option
                                    value="{{ $j->id_jenis }}"
                                    {{ $item->id_jenis == $j->id_jenis ? 'selected' : '' }}>

                                    {{ $j->nama_jenis }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="form-group">

                        <label>Umur Ekonomis (Tahun)</label>

                        <input
                            type="number"
                            name="umur_ekonomis"
                            class="form-control"
                            value="{{ $item->umur_ekonomis }}"
                            min="1"
                            required>

                    </div>


                    <div class="form-group">

                        <label>Persentase Penyusutan (%)</label>

                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            max="100"
                            name="persentase"
                            class="form-control"
                            value="{{ $item->persentase }}"
                            required>

                    </div>


                    <div class="form-group">

                        <label>Status</label>

                        <select
                            name="status"
                            class="form-control">

                            <option value="aktif"
                                {{ $item->status=='aktif' ? 'selected' : '' }}>
                                Aktif
                            </option>

                            <option value="nonaktif"
                                {{ $item->status=='nonaktif' ? 'selected' : '' }}>
                                Nonaktif
                            </option>

                        </select>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">

                        Batal

                    </button>

                    <button
                        type="submit"
                        class="btn btn-warning">

                        <i class="fas fa-save"></i>

                        Simpan Perubahan

                    </button>

                </div>

            </form>

        </div>
    </div>

</div>

@endforeach