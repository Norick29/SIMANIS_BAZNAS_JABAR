@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">
        <i class="fas fa-chart-line mr-2 text-secondary"></i> Riwayat Penyusutan Nilai Aset
    </h1>

    {{-- SESSION ALERT MESSAGES --}}
    @foreach (['success', 'error', 'warning'] as $type)
        @if (session($type))
            <div class="alert alert-{{ $type == 'error' ? 'danger' : $type }} alert-dismissible fade show">
                {{ session($type) }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif
    @endforeach

    {{-- CARD UTAMA --}}
    <div class="card shadow mb-4">
        {{-- HEADER CARD --}}
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div>
                <button class="btn btn-secondary btn-sm" type="button" data-toggle="collapse" data-target="#filterPenyusutan">
                    <i class="fas fa-filter"></i> Filter Periode
                </button>
            </div>
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-list"></i> Daftar Riwayat Penyusutan Nilai Buku 
                @if(request('kekritisan') == 'kritis')
                    - <span class="text-danger">Kondisi Kritis (&ge; 5 Tahun)</span>
                @elseif(request('kekritisan') == 'optimal')
                    - <span class="text-primary">Kondisi Optimal (< 5 Tahun)</span>
                @else
                    - Semua Masa Pakai
                @endif
            </h6>
        </div>

        {{-- CONTAINER TOGGLE COLLAPSE FILTER --}}
        <div class="collapse {{ request()->hasAny(['kekritisan', 'tindakan', 'status']) ? 'show' : '' }}" id="filterPenyusutan">
            <div class="card border-left-success shadow-sm mb-3">
                <div class="card-body bg-light">
                    <form method="GET" action="{{ url()->current() }}">
                        <div class="row">
                            {{-- FILTER 1: TINGKAT KEKRITISAN UMUR --}}
                            <div class="col-md-4">
            <label class="font-weight-bold">
                <i class="fas fa-hourglass-half"></i>
                Tingkat Kekritisan Umur
            </label>

            <select
                name="kekritisan"
                class="form-control auto-filter">

                <option value="">
                    Semua Masa Pakai
                </option>

                <option
                    value="kritis"
                    {{ request('kekritisan')=='kritis'?'selected':'' }}>

                    Kritis (Melebihi Umur Ekonomis)

                </option>

                <option
                    value="optimal"
                    {{ request('kekritisan')=='optimal'?'selected':'' }}>

                    Optimal (Belum Melebihi Umur Ekonomis)

                </option>

            </select>

        </div>

                    {{-- FILTER 2: JENIS USULAN TINDAKAN --}}
                    <div class="col-md-4">
                        <label class="font-weight-bold"><i class="fas fa-gavel"></i> Status Usulan Audit</label>
                        <select name="tindakan" class="form-control auto-filter">
                            <option value="">Semua Kondisi Tindakan</option>
                            <option value="murni_susut" {{ request('tindakan') == 'murni_susut' ? 'selected' : '' }}>Belum Ada Tindakan (Murni Penyusutan)</option>
                            <option value="peremajaan" {{ request('tindakan') == 'peremajaan' ? 'selected' : '' }}>Dalam Pengajuan Peremajaan</option>
                            <option value="penghapusan" {{ request('tindakan') == 'penghapusan' ? 'selected' : '' }}>Dalam Pengajuan Penghapusan</option>
                        </select>
                    </div>

                    {{-- FILTER 3: STATUS APPROVAL INTI --}}
                    <div class="col-md-4">
                        <label class="font-weight-bold"><i class="fas fa-check-double"></i> Status Alur Birokrasi</label>
                        <select name="status" class="form-control auto-filter">
                            <option value="">Semua Status Approval</option>
                            <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu Pimpinan</option>
                            <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Audit Selesai / Sah</option>
                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak Pimpinan</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3 text-right">
                    <a href="{{ route('penyusutan.index') }}" class="btn btn-secondary btn-sm">Reset Filter</a>
                    <button type="submit" class="btn btn-success btn-sm font-weight-bold">
                        <i class="fas fa-search"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

        {{-- BODY CARD --}}
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr class="text-center">
                            <th>No</th>
                            <th>Kode Aset</th>
                            <th>Nama Barang</th>
                            <th>Tahun Beli</th>
                            <th>Nilai Awal</th>
                            <th>Nilai Penyusutan</th>
                            <th>Nilai Akhir</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($riwayat as $item)
                            <tr class="text-center">
                                <td>{{ $riwayat->firstItem() + $loop->index }}</td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $item->asset->kode_aset ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-left">
                                    {{ $item->asset->barang->nama_barang ?? '-' }}
                                    @php
                                        $umurAset = $item->asset
                                            ? ((int)date('Y') - (int)$item->asset->tahun)
                                            : 0;

                                        $umurEkonomis = optional(
                                            optional($item->asset->jenis)
                                                ->masterPenyusutan
                                        )->umur_ekonomis ?? 5;
                                    @endphp

                                    @if($umurAset >= $umurEkonomis)

                                        <span class="badge badge-danger ml-1" style="font-size:10px;">
                                            <i class="fas fa-exclamation-circle"></i>
                                            Kritis
                                        </span>

                                    @endif
                                </td>
                                <td>{{ $item->asset->tahun ?? '-' }}</td>
                                <td class="text-right">Rp {{ number_format($item->nilai_awal, 0, ',', '.') }}</td>
                                <td class="text-right text-danger font-weight-bold">- Rp {{ number_format($item->nilai_penyusutan, 0, ',', '.') }}</td>
                                {{-- Ganti baris kolom nilai akhir penyusutan milikmu --}}
                                <td class="text-right font-weight-bold text-success">
                                    @if($item->status == 'disetujui' && \Illuminate\Support\Str::contains(strtolower($item->alasan ?? ''), 'peremajaan'))
                                        {{-- Jika sukses diremajakan, naikkan nilainya (Nilai sisa 10% + pemulihan 50% dari penyusutan) --}}
                                        Rp {{ number_format($item->nilai_setelah_penyusutan + ($item->nilai_penyusutan * 0.50), 0, ',', '.') }}
                                    @else
                                        {{-- Jika status normal / menunggu, tampilkan nilai setelah penyusutan murni dari database --}}
                                        Rp {{ number_format($item->nilai_setelah_penyusutan, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td>
                                    @php
                                        // Ambil umur real aset di baris ini
                                        $umurAsetReal = $item->asset ? ((int)date('Y') - (int)$item->asset->tahun) : 0;
                                    @endphp

                                    @if ($item->status == 'menunggu' && $item->requested_by === null)
                                        {{-- JIKA BELUM ADA TINDAKAN STAFF --}}
                                        @php
                                            $umurAset = $item->asset
                                                ? ((int)date('Y') - (int)$item->asset->tahun)
                                                : 0;

                                            $umurEkonomis = optional(
                                                optional($item->asset->jenis)
                                                    ->masterPenyusutan
                                            )->umur_ekonomis ?? 5;
                                        @endphp

                                        @if($umurAsetReal >= $umurEkonomis)
                                            {{-- Aset Tua: Wajib Ditindak --}}
                                            <span class="badge badge-danger shadow-sm font-weight-bold animated flash infinite">
                                                <i class="fas fa-gavel"></i> Perlu Audit (Kritis)
                                            </span>
                                        @else
                                            {{-- Aset Muda: Aman, Hanya Catatan Finansial --}}
                                            <span class="badge badge-light text-dark border shadow-sm">
                                                <i class="fas fa-check text-muted"></i> Terhitung (Optimal)
                                            </span>
                                        @endif

                                    @elseif ($item->status == 'disetujui')
                                        {{-- SUDAH DI-APPROVE OLEH KADIV --}}
                                        @if(\Illuminate\Support\Str::contains(strtolower($item->alasan ?? ''), 'peremajaan'))
                                            <span class="badge badge-success shadow-sm">
                                                <i class="fas fa-check-circle"></i> Peremajaan Selesai
                                            </span>
                                        @else
                                            <span class="badge badge-secondary shadow-sm">
                                                <i class="fas fa-ban"></i> Aset Dihapuskan
                                            </span>
                                        @endif

                                    @elseif ($item->status == 'ditolak')
                                        {{-- JIKA USULAN TINDAKAN DITOLAK PIMPINAN --}}
                                        @if(\Illuminate\Support\Str::contains(strtolower($item->alasan ?? ''), 'penghapusan'))
                                            <span class="badge badge-danger shadow-sm font-weight-bold">
                                                <i class="fas fa-times-circle"></i> Penghapusan Ditolak
                                            </span>
                                        @elseif(\Illuminate\Support\Str::contains(strtolower($item->alasan ?? ''), 'peremajaan'))
                                            <span class="badge badge-danger shadow-sm font-weight-bold">
                                                <i class="fas fa-times-circle"></i> Peremajaan Ditolak
                                            </span>
                                        @else
                                            <span class="badge badge-danger shadow-sm font-weight-bold">
                                                <i class="fas fa-times-circle"></i> Ditolak Pimpinan
                                            </span>
                                        @endif

                                    @elseif ($item->status == 'menunggu' && $item->requested_by !== null)
                                        {{-- SUDAH DIAJUKAN OLEH STAFF, MENUNGGU PIMPINAN --}}
                                        @if(\Illuminate\Support\Str::contains(strtolower($item->alasan ?? ''), 'penghapusan'))
                                            <span class="badge badge-danger shadow-sm">
                                                <i class="fas fa-spinner fa-spin"></i> Menunggu Approval Penghapusan
                                            </span>
                                        @elseif(\Illuminate\Support\Str::contains(strtolower($item->alasan ?? ''), 'peremajaan'))
                                            <span class="badge badge-warning shadow-sm">
                                                <i class="fas fa-spinner fa-spin"></i> Menunggu Approval Peremajaan
                                            </span>
                                        @else
                                            <span class="badge badge-warning shadow-sm">
                                                <i class="fas fa-spinner fa-spin"></i> Menunggu Approval
                                            </span>
                                        @endif

                                    @else
                                        {{-- JIKA BELUM ADA TINDAKAN SAMA SEKALI (MURNI ENGINE TAHUNAN) --}}
                                        <span class="badge badge-danger shadow-sm font-weight-bold animated flash infinite">
                                            <i class="fas fa-gavel"></i> Perlu Audit (Kritis)
                                        </span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    {{-- Tombol Detail memanggil modal overlay loop dinamis --}}
                                    <button class="btn btn-info btn-sm shadow-sm" data-toggle="modal" data-target="#detailModalPenyusutan{{ $item->id_penyusutan }}">
                                        <i class="fas fa-eye"></i> Detail / Audit
                                    </button>

                                    {{-- 🔑 TOMBOL GENERATE BERITA ACARA PEREMAJAAN DINAMIS --}}
                                    @if($item->status == 'disetujui' && \Illuminate\Support\Str::contains(strtolower($item->alasan ?? ''), 'peremajaan'))
                                        <a href="{{ route('peremajaan.cetak-ba', $item->id_penyusutan) }}" 
                                        class="btn btn-primary btn-sm shadow-sm ml-1" 
                                        target="_blank"
                                        title="Cetak Berita Acara Peremajaan">
                                            <i class="fas fa-file-pdf"></i> BA Peremajaan
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">
                                    Tidak ada data riwayat penyusutan aset untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- PAGINATION LINK --}}
                <div class="row mt-3">
                    <div class="col-md-6 text-muted">
                        Showing {{ $riwayat->firstItem() ?? 0 }} to {{ $riwayat->lastItem() ?? 0 }} of {{ $riwayat->total() }} entries
                    </div>
                    <div class="col-md-6 d-flex justify-content-end">
                        {{ $riwayat->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DETAIL OVERLAY LOOP --}}
@foreach ($riwayat as $item)
<div class="modal fade" id="detailModalPenyusutan{{ $item->id_penyusutan }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-info-circle mr-1"></i> Lembar Hasil Audit Keuangan & Fisik Aset</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-dark">
                <table class="table table-bordered">
                    <tr>
                        <th width="35%" class="bg-light">Kode Aset</th>
                        <td class="font-weight-bold text-success">{{ $item->asset->kode_aset ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Nama Barang</th>
                        <td>{{ $item->asset->barang->nama_barang ?? '-' }}</td>
                    </tr>
                    @php

                        $umurAset = $item->asset
                            ? ((int)date('Y') - (int)$item->asset->tahun)
                            : 0;

                        $umurEkonomis = optional(
                            optional($item->asset->jenis)
                                ->masterPenyusutan
                        )->umur_ekonomis ?? 0;

                    @endphp
                    <tr>
                        <th class="bg-light">Tahun Perolehan Awal</th>
                        <td>
                            {{ $item->asset->tahun ?? '-' }}
                            <br>

                            <small class="text-muted">
                                Umur Aset :
                                <strong>{{ $umurAset }} Tahun</strong>

                                |

                                Umur Ekonomis :
                                <strong>{{ $umurEkonomis }} Tahun</strong>
                            </small>

                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Nilai Buku Awal Periode</th>
                        <td class="font-weight-bold">Rp {{ number_format($item->nilai_awal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Nilai Beban Penyusutan Periode Ini</th>
                        <td class="text-danger font-weight-bold">- Rp {{ number_format($item->nilai_penyusutan, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Nilai Buku Setelah Penyusutan (10% Scrap Value)</th>
                        <td class="text-success font-weight-bold">Rp {{ number_format($item->nilai_setelah_penyusutan, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Jenis Pengajuan</th>
                        <td>
                            @if(\Illuminate\Support\Str::contains(strtolower($item->alasan ?? ''), 'penghapusan'))
                                <span class="badge badge-danger font-weight-bold">
                                    <i class="fas fa-trash mr-1"></i> Penghapusan Aset
                                </span>
                            @elseif(\Illuminate\Support\Str::contains(strtolower($item->alasan ?? ''), 'peremajaan'))
                                <span class="badge badge-warning font-weight-bold">
                                    <i class="fas fa-sync-alt mr-1"></i> Peremajaan Aset
                                </span>
                            @elseif($item->requested_by === null)
                                <span class="badge badge-info font-weight-bold">
                                    <i class="fas fa-calculator mr-1"></i> Penyusutan Otomatis
                                </span>
                            @else
                                <span class="badge badge-secondary font-weight-bold">
                                    <i class="fas fa-file-alt mr-1"></i> Pengajuan Aset
                                </span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Alasan Pengajuan / Deskripsi Audit</th>
                        <td class="text-justify">{{ $item->alasan }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Status Permintaan</th>
                        <td>
                            @if ($item->status == 'menunggu')
                                @if(\Illuminate\Support\Str::contains(strtolower($item->alasan ?? ''), 'penghapusan'))
                                    <span class="badge badge-danger">Menunggu Validasi Penghapusan Aset</span>
                                @elseif(\Illuminate\Support\Str::contains(strtolower($item->alasan ?? ''), 'peremajaan'))
                                    <span class="badge badge-warning">Menunggu Validasi Peremajaan Aset</span>
                                @elseif($item->requested_by === null)
                                    <span class="badge badge-info">Penyusutan Otomatis Tercatat</span>
                                @else
                                    <span class="badge badge-warning">Menunggu Validasi Usulan Kebijakan Pimpinan</span>
                                @endif
                            @elseif ($item->status == 'disetujui')
                                @if(\Illuminate\Support\Str::contains(strtolower($item->alasan ?? ''), 'peremajaan'))
                                    <span class="badge badge-success">Disetujui (Nilai Buku Diperbarui)</span>
                                @else
                                    <span class="badge badge-danger-light text-danger border border-danger font-weight-bold shadow-sm">
                                        <i class="fas fa-ban mr-1"></i> Disetujui (Aset Dinonaktifkan & Diarsipkan)
                                    </span>
                                @endif
                            @elseif ($item->status == 'ditolak')
                                {{-- Dipertegas kuncinya menggunakan string eksplisit 'ditolak' --}}
                                @if(\Illuminate\Support\Str::contains(strtolower($item->alasan ?? ''), 'penghapusan'))
                                    <span class="badge badge-danger font-weight-bold shadow-sm">
                                        <i class="fas fa-times-circle mr-1"></i> Penghapusan Ditolak Pimpinan
                                    </span>
                                @elseif(\Illuminate\Support\Str::contains(strtolower($item->alasan ?? ''), 'peremajaan'))
                                    <span class="badge badge-danger font-weight-bold shadow-sm">
                                        <i class="fas fa-times-circle mr-1"></i> Peremajaan Ditolak Pimpinan
                                    </span>
                                @else
                                    <span class="badge badge-danger font-weight-bold shadow-sm">
                                        <i class="fas fa-times-circle mr-1"></i> Ditolak Pimpinan
                                    </span>
                                @endif
                            @endif
                        </td>
                    </tr>
                </table>

                {{-- Tampilkan opsi tindakan apabila aset telah mencapai umur ekonomis --}}
                @php

                    $umurAsetReal = $item->asset
                        ? ((int)date('Y') - (int)$item->asset->tahun)
                        : 0;

                    $umurEkonomis = optional(
                        optional($item->asset->jenis)
                            ->masterPenyusutan
                    )->umur_ekonomis ?? 0;

                @endphp

               @if(
                    $item->status == 'menunggu'
                    && $item->requested_by === null
                    && $umurAsetReal >= $umurEkonomis
                    && auth()->user()->role !== 'kadept'
                    && auth()->user()->role !== 'kadiv'
                )
                    <hr class="my-4">
                    <h6 class="font-weight-bold text-dark mb-3">
                        <i class="fas fa-gavel text-warning mr-1"></i> Tindakan Opsi Hasil Audit Pengawasan Staf Logistik
                    </h6>
                    
                    <div class="row">
                        {{-- OPSI 1: PEREMAJAAN --}}
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-warning shadow-sm h-100">
                                <div class="card-body p-3 d-flex flex-column justify-content-between">
                                    <div>
                                        <h6 class="font-weight-bold text-warning"><i class="fas fa-sync-alt mr-1"></i> Opsi 1: Ajukan Peremajaan</h6>
                                        <small class="text-muted d-block mb-3">Menyetujui perbaikan atau rehabilitasi aset sehingga umur ekonomis aset dapat diperpanjang sesuai kebijakan penyusutan yang berlaku.</small>
                                    </div>
                                    <form action="{{ route('penyusutan.peremajaan', $item->id_penyusutan) }}" method="POST">
                                        @csrf
                                        {{-- 🔑 REVISI: Ubah name="catatan" menjadi name="alasan" agar sinkron dengan Controller --}}
                                        <input type="text" name="alasan" class="form-control form-control-sm mb-2" placeholder="Contoh: Kondisi fisik dirawat, lensa diganti baru..." required>
                                        <button type="submit" class="btn btn-warning btn-sm btn-block font-weight-bold text-dark shadow-sm">Eksekusi Peremajaan</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- OPSI 2: PENGHAPUSAN --}}
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-danger shadow-sm h-100">
                                <div class="card-body p-3 d-flex flex-column justify-content-between">
                                    <div>
                                        <h6 class="font-weight-bold text-danger"><i class="fas fa-trash-alt mr-1"></i> Opsi 2: Ajukan Penghapusan</h6>
                                        <small class="text-muted d-block mb-3">Memusnahkan aset secara permanen dari daftar operasional aktif lembaga karena barang tidak dapat digunakan kembali.</small>
                                    </div>
                                    <form action="{{ route('penyusutan.penghapusan', $item->id_penyusutan) }}" method="POST">
                                        @csrf
                                        {{-- 🔑 KEY FIX: Ubah name="catatan" menjadi name="alasan" agar dibaca oleh Eloquent Controller --}}
                                        <input type="text" name="alasan" class="form-control form-control-sm mb-2" placeholder="Contoh: Barang rusak total/hancur, tidak ekonomis diperbaiki..." required>
                                        <button type="submit" class="btn btn-danger btn-sm btn-block font-weight-bold shadow-sm">Eksekusi Penghapusan</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- AUTO SUBMIT DROPDOWN JAVASCRIPT --}}
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script>
    $(document).ready(function () {
        $('.auto-filter').on('change', function() {
            $(this).closest('form').submit();
        });
    });
</script>
@endsection
