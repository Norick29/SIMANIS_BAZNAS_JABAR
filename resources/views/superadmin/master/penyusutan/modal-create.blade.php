<div class="modal fade" id="createMasterPenyusutanModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Tambah Kebijakan Penyusutan
                </h5>

                <button class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form action="{{ route('master-penyusutan.store') }}" method="POST">

                @csrf

                <div class="modal-body">

    <div id="masterWrapper">

        <div class="row mb-2 master-row">

            <div class="col-md-4">
                <select name="items[0][id_jenis]" class="form-control" required>
                    <option value="">-- Pilih Jenis --</option>

                    @foreach($jenis as $j)
                        <option value="{{ $j->id_jenis }}">
                            {{ $j->nama_jenis }}
                        </option>
                    @endforeach

                </select>
            </div>

            <div class="col-md-2">
                <input type="number"
                       name="items[0][umur_ekonomis]"
                       class="form-control"
                       placeholder="Umur"
                       min="1"
                       required>
            </div>

            <div class="col-md-2">
                <input type="number"
                       name="items[0][persentase]"
                       class="form-control"
                       placeholder="%"
                       min="0"
                       max="100"
                       step="0.01"
                       required>
            </div>

            <div class="col-md-2">

                <select
                    name="items[0][status]"
                    class="form-control">

                    <option value="aktif">
                        Aktif
                    </option>

                    <option value="nonaktif">
                        Nonaktif
                    </option>

                </select>

            </div>

            <div class="col-md-2">

                <button
                    type="button"
                    class="btn btn-danger btn-remove">

                    ×

                </button>

            </div>

        </div>

    </div>

    @if($jenis->isEmpty())

    <div class="alert alert-success">

        <i class="fas fa-check-circle"></i>

        Semua jenis aset telah memiliki master penyusutan.

    </div>

    @endif

    <button
        type="button"
        class="btn btn-outline-success btn-sm mt-2"
        id="addMaster">

        + Tambah Baris

    </button>

</div>


                <div class="modal-footer">

                    <button
                        class="btn btn-secondary"
                        data-dismiss="modal">

                        Batal

                    </button>

                    <button
                        class="btn btn-success"
                        type="submit">

                        <i class="fas fa-save"></i>

                        Simpan

                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

<script>

let masterIndex = 1;

document.getElementById('addMaster').addEventListener('click', function(){

    let wrapper = document.getElementById('masterWrapper');

    let row = document.createElement('div');

    row.classList.add('row','mb-2','master-row');

    row.innerHTML = `

        <div class="col-md-4">

            <select
                name="items[${masterIndex}][id_jenis]"
                class="form-control"
                required>

                <option value="">-- Pilih Jenis --</option>

                @foreach($jenis as $j)

                    <option value="{{ $j->id_jenis }}">

                        {{ $j->nama_jenis }}

                    </option>

                @endforeach

            </select>

        </div>

        <div class="col-md-2">

            <input
                type="number"
                name="items[${masterIndex}][umur_ekonomis]"
                class="form-control"
                min="1"
                placeholder="Umur"
                required>

        </div>

        <div class="col-md-2">

            <input
                type="number"
                name="items[${masterIndex}][persentase]"
                class="form-control"
                min="0"
                max="100"
                step="0.01"
                placeholder="%"
                required>

        </div>

        <div class="col-md-2">

            <select
                name="items[${masterIndex}][status]"
                class="form-control">

                <option value="aktif">Aktif</option>

                <option value="nonaktif">Nonaktif</option>

            </select>

        </div>

        <div class="col-md-2">

            <button
                type="button"
                class="btn btn-danger btn-remove">

                ×

            </button>

        </div>

    `;

    wrapper.appendChild(row);

    masterIndex++;

});

document.addEventListener('click',function(e){

    if(e.target.classList.contains('btn-remove')){

        let rows = document.querySelectorAll('.master-row');

        if(rows.length>1){

            e.target.closest('.master-row').remove();

        }

    }

});

</script>