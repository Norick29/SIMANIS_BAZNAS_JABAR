@extends('layouts.app') {{-- Menyesuaikan dengan layout app kamu --}}

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-search-location"></i> Manajemen Berita Acara Aset Hilang
        </h1>
    </div> 

    {{-- ALERT MESSAGE LOGIC --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif
    
    @if ($errors->any())
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <p><strong>Waduh, form gagal disimpan karena:</strong></p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- DATA TABLE CARD --}}
    <div class="card shadow mb-4">
        
        {{-- HEADER CARD --}}
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div>
                <button class="btn btn-secondary btn-sm" type="button" data-toggle="collapse" data-target="#filterAsetHilang">
                    <i class="fas fa-filter"></i> Filter Periode
                </button>
            </div>

            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-list"></i> Daftar Aset Hilang - Periode {{ $monthsList[$selectedMonth] }} {{ $selectedYear }}
            </h6>
        </div>

        {{-- TOGGLE COLLAPSE FILTER --}}
        <div class="collapse {{ request()->hasAny(['bulan', 'tahun', 'status_isi']) ? 'show' : '' }}" id="filterAsetHilang">
            <div class="card-body bg-light border-bottom">
                <form method="GET" action="{{ route('hilang.index') }}">
                    <div class="row">
                        {{-- FILTER BULAN --}}
                        <div class="col-md-4">
                            <label class="font-weight-bold">Bulan</label>
                            <select name="bulan" class="form-control auto-filter">
                                @foreach($monthsList as $num => $name)
                                    <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- FILTER TAHUN --}}
                        <div class="col-md-4">
                            <label class="font-weight-bold">Tahun</label>
                            <select name="tahun" class="form-control auto-filter">
                                @foreach($yearsList as $yr)
                                    <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>
                                        {{ $yr }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- FILTER STATUS PENGISIAN BERITA ACARA --}}
                        <div class="col-md-4">
                            <label class="font-weight-bold">Status Berita Acara</label>
                            <select name="status_isi" class="form-control auto-filter">
                                <option value="">Semua Status</option>
                                <option value="belum" {{ request('status_isi') == 'belum' ? 'selected' : '' }}>Belum Diisi (Draft)</option>
                                <option value="selesai" {{ request('status_isi') == 'selesai' ? 'selected' : '' }}>Selesai & Dikunci</option>
                            </select>
                        </div>
                    </div>

                    {{-- TOMBOL AKSI FORM --}}
                    <div class="mt-3 text-right">
                        <a href="{{ route('hilang.index') }}" class="btn btn-secondary">Reset</a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-search"></i> Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- CARD BODY UTAMA --}}
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tabelAsetHilang">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>No</th>
                            <th>Kode Aset</th>
                            <th>Nama Barang</th>
                            <th>Lokasi</th>
                            <th>Merk / Model</th>
                            <th>Status Pengisian</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($assetsHilang as $item)
                            <tr class="text-center">
                                <td>{{ $assetsHilang->firstItem() + $loop->index }}</td>
                                <td class="font-weight-bold text-success">{{ $item->kode_aset }}</td>
                                <td class="text-left">{{ $item->barang->nama_barang ?? '-' }}</td>
                                <td>{{ $item->lokasi->nama_lokasi ?? '-' }}</td>
                                <td>{{ $item->merk ?? '-' }}</td>
                                <td>
                                    @if($item->asetHilangRecord)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle"></i> Selesai & Dikunci
                                        </span>
                                    @else
                                        <span class="badge badge-warning">
                                            <i class="fas fa-spinner fa-spin"></i> Form Belum Diisi
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->asetHilangRecord)
                                        {{-- JIKA SUDAH DIISI: Tampilkan Tombol Detail (Halaman Penuh), Cetak PDF, dan Ditemukan --}}
                                        <div class="btn-group" role="group">
                                            {{-- ⚡ TOMBOL DETAIL BARU: Menuju ke Halaman Berita Acara Penuh --}}
                                            <a href="{{ route('hilang.berita-acara', $item->asetHilangRecord->id_hilang) }}" class="btn btn-info btn-sm mr-2">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                            <a href="{{ route('hilang.cetak', $item->asetHilangRecord->id_hilang) }}" class="btn btn-danger btn-sm mr-2" target="_blank">
                                                <i class="fas fa-file-pdf"></i> Cetak PDF
                                            </a>
                                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalDitemukan{{ $item->asetHilangRecord->id_hilang }}">
                                                <i class="fas fa-box-open"></i> Ditemukan
                                            </button>
                                        </div>

                                        {{-- MODAL KONFIRMASI ASET DITEMUKAN KEMBALI --}}
                                        <div class="modal fade" id="modalDitemukan{{ $item->asetHilangRecord->id_hilang }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-success text-white">
                                                        <h5 class="modal-title"><i class="fas fa-undo"></i> Pemulihan Status Aset</h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('hilang.ditemukan', $item->asetHilangRecord->id_hilang) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body text-left">
                                                            <p>Apakah Anda yakin bahwa aset dengan kode <strong>{{ $item->kode_aset }}</strong> ini telah ditemukan kembali?</p>
                                                            <p class="mb-0 text-muted small"><em>*Tindakan ini akan mengembalikan kondisi fisik aset menjadi <strong>Baik</strong> dan status operasional menjadi <strong>Aktif</strong>. Catatan kronologi lama akan diarsipkan di log audit.</em></p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-success">Ya, Sudah Ditemukan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        {{-- JIKA BELUM DIISI: Munculkan Tombol Pengisian --}}
                                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalFormHilang{{ $item->id_aset }}">
                                            <i class="fas fa-edit"></i> Isi Kronologi
                                        </button>

                                        <div class="modal fade" id="modalFormHilang{{ $item->id_aset }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title">Form Berita Acara Kehilangan: {{ $item->kode_aset }}</h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="{{ route('hilang.store') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="id_aset" value="{{ $item->id_aset }}">
                                                        
                                                        <div class="modal-body text-left">
                                                            <div class="form-group">
                                                                <label class="font-weight-bold">Nama Aset / Barang</label>
                                                                <input type="text" class="form-control" value="{{ $item->barang->nama_barang ?? '' }} - {{ $item->merk }}" readonly>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="tanggal_hilang" class="font-weight-bold">Tanggal Hilang <span class="text-danger">*</span></label>
                                                                <input type="date" class="form-control" name="tanggal_hilang" required>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="kronologi" class="font-weight-bold">Kronologi Kejadian Kehilangan <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" name="kronologi" rows="5" placeholder="Jelaskan kronologi kejadian secara mendalam..." required></textarea>
                                                                <small class="text-danger mt-1 d-block">⚠️ Setelah disimpan, form ini terkunci permanen demi validitas audit hukum aset.</small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-success">Simpan & Kunci Dokumen</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        @if ($assetsHilang->count() == 0)
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    Tidak ada data insiden aset hilang untuk periode ini.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                {{-- PAGINATION LINK --}}
                <div class="row mt-3">
                    <div class="col-md-6 text-muted text-left">
                        Showing {{ $assetsHilang->firstItem() ?? 0 }} to {{ $assetsHilang->lastItem() ?? 0 }} of {{ $assetsHilang->total() }} entries
                    </div>
                    <div class="col-md-6 d-flex justify-content-end">
                        {{ $assetsHilang->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

{{-- SCRIPT DIBAWAH JAUH LEBIH BERSIH KARENA COATING AJAX SUDAH DIAPUS TOTAL --}}
@push('scripts')
<script>
    $(document).ready(function () {
        // Inisialisasi DataTables secara defensif menggunakan ID presisi
        if (!$.fn.DataTable.isDataTable('#tabelAsetHilang')) {
            $('#tabelAsetHilang').DataTable({
                "paging": false,
                "info": false,
                "responsive": true,
                "autoWidth": false
            });
        }

        // Auto-submit form ketika user mengganti pilihan dropdown
        $('.auto-filter').on('change', function() {
            $(this).closest('form').submit();
        });
    });
</script>
@endpush

@endsection