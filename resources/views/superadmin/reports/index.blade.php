@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-contract mr-2"></i> Manajemen Laporan & Audit Aset
        </h1>
    </div>

    <div class="row">
        {{-- SEKTOR 1: LAPORAN INVENTARIS ASET --}}
        <div class="col-xl-6 col-md-12 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center mb-3">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Master Report & Valuation
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Laporan Inventaris Aset</div>
                            <small class="text-muted">Mencetak rekap data seluruh aset operasional, volume, beserta nilai finansial total aset.</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    
                    <hr>

                    <form action="{{ route('reports.asset.print') }}" method="GET" target="_blank">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label font-weight-bold text-xs">Filter Lokasi / Ruangan</label>
                                <select name="id_lokasi" class="form-control form-control-sm">
                                    <option value="">-- Semua Lokasi --</option>
                                    @foreach($lokasis as $l)
                                        <option value="{{ $l->id_lokasi }}">{{ $l->nama_lokasi }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label class="form-label font-weight-bold text-xs">Filter Kategori</label>
                                <select name="id_kategori" class="form-control form-control-sm">
                                    <option value="">-- Semua Kategori --</option>
                                    @foreach($kategoris as $k)
                                        <option value="{{ $k->id_kategori }}">{{ $k->nama_kategori }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label class="form-label font-weight-bold text-xs">Filter Sumber Dana</label>
                                <select name="id_sumber_dana" class="form-control form-control-sm">
                                    <option value="">-- Semua Sumber Dana --</option>
                                    @foreach($sumbers as $s)
                                        <option value="{{ $s->id_sumber_dana }}">{{ $s->nama_sumber_dana }}</option>
                                    @endforeach {{-- 🔑 FIXED: Penutupan foreach dipindah ke posisi yang benar --}}
                                </select> 
                            </div>

                            <div class="col-md-3 mb-2">
                                <label class="form-label font-weight-bold text-xs">Tahun Pembelian</label>
                                <select name="tahun" class="form-control form-control-sm">
                                    <option value="">-- Semua --</option>
                                    @foreach($tahuns as $t)
                                        <option value="{{ $t }}">{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-2">
                                <label class="form-label font-weight-bold text-xs">Kondisi Fisik</label>
                                <select name="kondisi" class="form-control form-control-sm">
                                    <option value="">-- Semua --</option>
                                    <option value="baik">Baik</option>
                                    <option value="rusak">Rusak</option>
                                    <option value="hilang">Hilang</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-success btn-sm btn-block shadow-sm">
                                <i class="fas fa-print fa-sm"></i> Buka Halaman Cetak Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- SEKTOR 2: LAPORAN MAINTENANCE ASET --}}
        <div class="col-xl-6 col-md-12 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center mb-3">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Operational Cost Control
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Laporan Maintenance Aset</div>
                            <small class="text-muted">Mencetak riwayat perbaikan aset, deskripsi kerusakan, status, serta kalkulasi total biaya pengeluaran servis.</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    
                    <hr>

                    <form action="{{ route('reports.maintenance.print') }}" method="GET" target="_blank">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label font-weight-bold text-xs">Mulai Periode (Tanggal)</label>
                                <input type="date" name="tanggal_awal" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label class="form-label font-weight-bold text-xs">Sampai Periode (Tanggal)</label>
                                <input type="date" name="tanggal_akhir" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-12 mb-2">
                                <label class="form-label font-weight-bold text-xs">Filter Status Perbaikan</label>
                                <select name="status" class="form-control form-control-sm">
                                    <option value="">-- Semua Status --</option>
                                    <option value="proses">Dalam Proses Perbaikan</option>
                                    <option value="selesai">Selesai Diperbaiki</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-warning btn-sm btn-block text-white font-weight-bold shadow-sm">
                                <i class="fas fa-print fa-sm"></i> Buka Halaman Cetak Maintenance
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- SEKTOR 3: LAPORAN MUTASI / PERPINDAHAN ASET --}}
        <div class="col-xl-6 col-md-12 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center mb-3">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Internal Control & Accountability
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Laporan Jurnal Mutasi / Perpindahan Aset</div>
                            <small class="text-muted">Mencetak log histori pergerakan logistik, peralihan hak penanggung jawab amil, perubahan lokasi ruangan, serta status verifikasinya.</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    
                    <hr>

                    <form action="{{ route('reports.mutasi.print') }}" method="GET" target="_blank">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label font-weight-bold text-xs">Mulai Tanggal Pengajuan</label>
                                <input type="date" name="tanggal_awal" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-4 mb-2">
                                <label class="form-label font-weight-bold text-xs">Sampai Tanggal Pengajuan</label>
                                <input type="date" name="tanggal_akhir" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-4 mb-2">
                                <label class="form-label font-weight-bold text-xs">Filter Status Mutasi</label>
                                <select name="status" class="form-control form-control-sm">
                                    <option value="">-- Semua Status --</option>
                                    <option value="menunggu">Menunggu Approval</option>
                                    <option value="disetujui">Disetujui (Selesai)</option>
                                    <option value="ditolak">Ditolak</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-info btn-sm btn-block font-weight-bold shadow-sm">
                                <i class="fas fa-print fa-sm"></i> Buka Halaman Cetak Jurnal Mutasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 🔑 CARD 4: LAPORAN JURNAL PENYUSUTAN & PEREMAJAAN ASET --}}
        <div class="col-xl-6 col-md-12 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center mb-3">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Financial Depreciation & Recovery
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Laporan Penyusutan & Peremajaan</div>
                            <small class="text-muted">Mencetak rekapitulasi beban penyusutan tahunan serta log usulan peremajaan fisik nilai buku aset.</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calculator fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <hr>
                    <form action="{{ route('reports.penyusutan.print') }}" method="GET" target="_blank">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label font-weight-bold text-xs">Filter Jenis Tindakan</label>
                                <select name="tindakan" class="form-control form-control-sm">
                                    <option value="">-- Semua Tindakan --</option>
                                    <option value="murni_susut">Penyusutan Otomatis Tahunan</option>
                                    <option value="peremajaan">Kebijakan Peremajaan Fisik</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label font-weight-bold text-xs">Filter Status Keputusan</label>
                                <select name="status" class="form-control form-control-sm">
                                    <option value="">-- Semua Status --</option>
                                    <option value="menunggu">Menunggu Approval</option>
                                    <option value="disetujui">Disetujui (Selesai)</option>
                                    <option value="ditolak">Ditolak Pimpinan</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label font-weight-bold text-xs">Filter Ruangan / Lokasi</label>
                                <select name="id_lokasi" class="form-control form-control-sm">
                                    <option value="">-- Semua Ruangan --</option>
                                    @foreach($lokasis as $l)
                                        <option value="{{ $l->id_lokasi }}">{{ $l->nama_lokasi }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- ⚡ NEW FILTER: JENIS ASET UNTUK DEPRESIASI --}}
                            <div class="col-md-6 mb-2">
                                <label class="form-label font-weight-bold text-xs">Filter Jenis / Golongan Aset</label>
                                <select name="id_jenis" class="form-control form-control-sm">
                                    <option value="">-- Semua Jenis Aset --</option>
                                    @foreach($jenises as $j)
                                        <option value="{{ $j->id_jenis }}">{{ $j->nama_jenis ?? $j->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-danger btn-sm btn-block font-weight-bold shadow-sm">
                                <i class="fas fa-print fa-sm"></i> Buka Halaman Cetak Penyusutan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 🔑 CARD 5: LAPORAN JURNAL PENGHAPUSAN / PEMUSNAHAN ASET --}}
        <div class="col-xl-6 col-md-12 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center mb-3">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                Asset Write-Off & Disposal Control
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Laporan Penghapusan Aset</div>
                            <small class="text-muted">Mencetak rekapitulasi data aset rusak total atau hilang yang telah resmi diajukan pemusnahan dokumennya.</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-trash-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <hr>
                    <form action="{{ route('reports.penghapusan.print') }}" method="GET" target="_blank">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label font-weight-bold text-xs">Filter Status Keputusan</label>
                                <select name="status" class="form-control form-control-sm">
                                    <option value="">-- Semua Status --</option>
                                    <option value="menunggu">Menunggu Approval</option>
                                    <option value="disetujui">Disetujui (Selesai)</option>
                                    <option value="ditolak">Ditolak Pimpinan</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label font-weight-bold text-xs">Filter Lokasi Kejadian</label>
                                <select name="id_lokasi" class="form-control form-control-sm">
                                    <option value="">-- Semua Lokasi --</option>
                                    @foreach($lokasis as $l)
                                        <option value="{{ $l->id_lokasi }}">{{ $l->nama_lokasi }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- ⚡ NEW FILTER: JENIS ASET UNTUK DISPOSAL --}}
                            <div class="col-md-4 mb-2">
                                <label class="form-label font-weight-bold text-xs">Filter Jenis Aset</label>
                                <select name="id_jenis" class="form-control form-control-sm">
                                    <option value="">-- Semua Jenis --</option>
                                    @foreach($jenises as $j)
                                        <option value="{{ $j->id_jenis }}">{{ $j->nama_jenis ?? $j->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-dark btn-sm btn-block font-weight-bold shadow-sm">
                                <i class="fas fa-print fa-sm"></i> Buka Halaman Cetak Penghapusan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection