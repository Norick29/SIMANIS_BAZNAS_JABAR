<div class="modal fade" id="createAssetModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Tambah Aset</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form action="{{ route('assets.store') }}" method="POST">
                @csrf

                <div class="modal-body">

                    {{-- ===== DATA REFERENSI ===== --}}
                    <h6 class="font-weight-bold text-success mb-2">Data Referensi</h6>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label>Barang</label>
                            <select name="id_barang" class="form-control" required>
                                <option value="">-- Pilih Barang --</option>
                                @foreach ($barangs as $b)
                                    <option value="{{ $b->id_barang }}">{{ $b->nama_barang }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Kategori</label>
                            <select name="id_kategori" class="form-control" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategoris as $k)
                                    <option value="{{ $k->id_kategori }}">{{ $k->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Jenis</label>
                            <select name="id_jenis" class="form-control" required>
                                <option value="">-- Pilih Jenis --</option>
                                @foreach ($jenis as $j)
                                    <option value="{{ $j->id_jenis }}">{{ $j->nama_jenis }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Lokasi</label>
                            <select name="id_lokasi" class="form-control" required>
                                <option value="">-- Pilih Lokasi --</option>
                                @foreach ($lokasis as $l)
                                    <option value="{{ $l->id_lokasi }}">{{ $l->nama_lokasi }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Penanggung Jawab (Amil)</label>
                            <select name="id_amil" class="form-control" required>
                                <option value="">-- Pilih Amil --</option>
                                @foreach ($amils as $a)
                                    <option value="{{ $a->id_amil }}">{{ $a->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Sumber Dana</label>
                            <select name="id_sumber_dana" class="form-control" required>
                                <option value="">-- Pilih Sumber Dana --</option>
                                @foreach ($sumbers as $s)
                                    <option value="{{ $s->id_sumber_dana }}">{{ $s->kode_sumber_dana }}-{{ $s->nama_sumber_dana }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr>

                    {{-- ===== DATA ASET ===== --}}
                    <h6 class="font-weight-bold text-success mb-2">Detail Aset</h6>

                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <label>Merk</label>
                            <input type="text" name="merk" class="form-control">
                        </div>

                        <div class="col-md-3 mb-2">
                            <label>Tahun</label>
                            <input type="text" name="tahun" class="form-control" maxlength="4">
                        </div>

                        <div class="col-md-3 mb-2">
                            <label>Volume</label>
                            <input type="number" name="volume" id="volume" class="form-control" value="1" min="1">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Satuan</label>
                            <input type="text" name="satuan" class="form-control">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Harga</label>
                            <input type="number" name="harga" id="harga" class="form-control" min="0">
                        </div>

                        <div class="col-md-4 mb-2">
                            <label>Total Harga</label>
                            <input type="number" id="total_harga" class="form-control" readonly>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Kondisi</label>
                            <select name="kondisi" class="form-control">
                                <option value="baik">Baik</option>
                                <option value="rusak">Rusak</option>
                                <option value="hilang">Hilang</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <hr>

                    {{-- ===== DATA SISTEM ===== --}}
                    <div class="row">
                        <div class="col-md-6">
                            <label>Kode Aset</label>
                            <input type="text" name="kode_aset" id="kode_aset" class="form-control" readonly>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
    function generateKodeAset() {
        const rand = Math.random().toString(36).substring(2, 7).toUpperCase();
        document.getElementById('kode_aset').value = 'AST-' + rand;
    }
    
    function hitungTotal() {
        const v = document.getElementById('volume').value || 0;
        const h = document.getElementById('harga').value || 0;
        document.getElementById('total_harga').value = v * h;
    }
    
    document.getElementById('volume').addEventListener('input', hitungTotal);
    document.getElementById('harga').addEventListener('input', hitungTotal);
    
    $('#createAssetModal').on('shown.bs.modal', function () {
        generateKodeAset();
    });
    </script>
    