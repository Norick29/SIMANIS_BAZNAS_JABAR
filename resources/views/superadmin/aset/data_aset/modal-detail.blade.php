<div class="modal fade" id="modalDetailAset" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        
        <div class="modal-header">
          <h5 class="modal-title">Detail Aset</h5>
          <button class="close" data-dismiss="modal">&times;</button>
        </div>
  
        <div class="modal-body">
          <div class="row">
  
            {{-- QR CODE --}}
            <div class="col-12 text-center mb-4">
              <img id="qrCode" src="" width="120">
            </div>
  
            {{-- KIRI --}}
            <div class="col-md-6">
              @foreach ([
                'Kode Aset' => 'kode_aset',
                'Barang' => 'barang',
                'Kategori' => 'kategori',
                'Lokasi' => 'lokasi',
                'Penanggung Jawab' => 'penanggung_jawab',
                'Volume' => 'volume',
                'Satuan' => 'satuan'
              ] as $label => $field)
                <div class="mb-2">
                  <strong>{{ $label }}</strong>
                  <div class="text-muted" id="{{ $field }}"></div>
                </div>
              @endforeach
            </div>
  
            {{-- KANAN --}}
            <div class="col-md-6">
              @foreach ([
                'Harga' => 'harga',
                'Status' => 'status',
                'Kondisi' => 'kondisi',
                'Jenis Aset' => 'jenis_aset',
                'Tahun Perolehan' => 'tahun_perolehan',
                'Sumber Dana' => 'sumber_dana'
              ] as $label => $field)
                <div class="mb-2">
                  <strong>{{ $label }}</strong>
                  <div class="text-muted" id="{{ $field }}"></div>
                </div>
              @endforeach
            </div>
  
          </div>
        </div>
  
        <div class="modal-footer">
          <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
          <button class="btn btn-warning">Ajukan Perpindahan</button>
        </div>
  
      </div>
    </div>
  </div>
  
  $('#modalDetailAset').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget);
    const aset = button.data('aset');
  
    Object.keys(aset).forEach(key => {
      $('#' + key).text(aset[key] ?? '-');
    });
  
    $('#qrCode').attr('src', `/qr-assets/${aset.qr_code}`);
  });
  