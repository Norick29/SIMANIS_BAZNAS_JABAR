<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan_Inventaris_Aset_BAZNAS_{{ date('d_M_Y') }}</title>
    <style>
        /* =========================================================================
         * PENGATURAN MARGIN KERTAS RESMI INSTANSI (ANTI-MEPET & ANTI-TABRAKAN)
         * ========================================================================= */
        @page {
            size: A4;
            /* Memberikan margin resmi 2.5 cm di atas, 2 cm di kanan-kiri, dan 2.5 cm di bawah */
            margin: 2.5cm 2cm 2.5cm 2cm;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* KOP SURAT ATAS TENGAH ASLI BAZNAS */
        .kop-surat {
            text-align: center;
            margin-bottom: 10px;
        }
        .kop-logo {
            width: 80px;
            height: auto;
            margin: 0 auto 5px auto;
        }
        .kop-logo img {
            width: 100%;
            height: auto;
        }
        .kop-text-1 {
            font-size: 13px;
            font-weight: bold;
            margin: 0 0 2px 0;
        }
        .kop-text-2 {
            font-size: 11px;
            font-weight: bold;
            color: #444;
            margin: 0;
        }

        .line-separator {
            border: none;
            border-top: 1px solid #000;
            margin: 5px 0 15px 0;
        }

        .judul-dokumen {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .subtitle-laporan {
            text-align: center;
            font-size: 10.5px;
            margin-bottom: 20px;
            color: #444;
            font-style: italic;
        }

        /* GAYA TABEL FISIK AUDIT (GARIS HITAM TEGAS) */
        .table-aset {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .table-aset th {
            border: 1px solid #000;
            padding: 6px 4px;
            font-size: 10px;
            font-weight: bold;
            background-color: #f2f2f2;
            text-align: center;
            text-transform: uppercase;
        }
        .table-aset td {
            border: 1px solid #000;
            padding: 6px 5px;
            font-size: 9.5px;
            vertical-align: middle;
        }
        
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-weight-bold { font-weight: bold; }
        .bg-summary { background-color: #e9ecef; }

        /* REKAYASA STRUKTUR BAWAH (TTD & FOOTER) */
        .bottom-section {
            /* Memaksa elemen tanda tangan dan footer tidak terpecah aneh di ganti halaman */
            page-break-inside: avoid; 
            margin-top: 30px;
        }

        .lokasi-tanggal {
            text-align: center;
            margin-left: 50%;
            margin-bottom: 10px;
            font-size: 11px;
        }
        .table-ttd {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .ttd-cell {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .ttd-title {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 5px;
        }
        .ttd-space {
            height: 70px;
            margin: 5px 0;
        }
        .ttd-line {
            width: 180px;
            margin: 0 auto;
            border-bottom: 1px dashed #000;
            padding-top: 3px;
            font-weight: bold;
        }
        .ttd-jabatan {
            font-size: 10.5px;
            font-style: italic;
            margin-top: 2px;
        }

        /* FOOTER DINAMIS: MENGIKUTI ALUR KONTEN SECARA AMAN */
        .footer-container {
            margin-top: 40px;
            width: 100%;
        }
        .footer-line {
            border: none;
            border-top: 1px solid #ccc;
            margin-bottom: 5px;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-text {
            width: 100%;
            font-size: 8px;
            color: #666;
            text-align: left;
            line-height: 1.3;
        }

        /* RESET TOTAL PRINT BROWSER */
        @media print {
            body {
                background: none;
                color: #000;
            }
        }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('image/logo-baznas.png');
        $logoSrc = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : asset('image/logo-baznas.png');
    @endphp

    {{-- KOP SURAT MODEL ATAS-TENGAH BAWAAN BAZNAS --}}
    <div class="kop-surat">
        <div class="kop-logo">
            <img src="{{ $logoSrc }}" alt="Logo BAZNAS">
        </div>
        <div class="kop-text-1">BADAN AMIL ZAKAT NASIONAL</div>
        <div class="kop-text-2">PROVINSI JAWA BARAT</div>
    </div>

    <div class="line-separator"></div>

    {{-- JUDUL DOKUMEN REKAPITULASI --}}
    <div class="judul-dokumen">
        LAPORAN REKAPITULASI INVENTARISASI ASET TETAP
    </div>
    <div class="subtitle-laporan">
        Parameter Filter: [ {{ $stringFilter }} ]
    </div>

    {{-- TABEL DATA BERGAYA AUDIT FISIK --}}
    <table class="table-aset">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 20%;">Kode Registrasi Aset</th>
                <th style="width: 22%;">Nama Barang / Spesifikasi</th>
                <th style="width: 12%;">Kategori</th>
                <th style="width: 12%;">Lokasi Ruangan</th>
                <th style="width: 12%;">Penanggung Jawab</th>
                <th style="width: 11%;">Sumber Dana</th>
                <th style="width: 5%;">Tahun</th>
                <th style="width: 5%;">Kondisi</th>
                <th style="width: 6%;">Vol</th>
                <th style="width: 11%;">Harga Satuan (Rp)</th>
                <th style="width: 13%;">Nilai Buku (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $index => $asset)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center font-weight-bold" style="letter-spacing: 0.3px;">{{ $asset->kode_aset }}</td>
                    <td class="text-left">
                        <strong>{{ $asset->barang->nama_barang ?? '-' }}</strong><br>
                        <small style="color:#555; font-style:italic;">Detail: {{ $asset->merk }}</small>
                    </td>
                    <td class="text-center">{{ $asset->kategori->nama_kategori ?? '-' }}</td>
                    <td class="text-center">{{ $asset->lokasi->nama_lokasi ?? '-' }}</td>
                    <td class="text-center font-weight-bold" style="color: #444;">{{ $asset->amil->nama ?? '-' }}</td>
                    <td class="text-center">{{ $asset->sumberDana->nama_sumber_dana ?? '-' }}</td>
                    <td class="text-center">{{ $asset->tahun }}</td>
                    <td class="text-center font-weight-bold" style="text-transform: uppercase; font-size: 9px;">
                        {{ $asset->kondisi }}
                    </td>
                    <td class="text-center">{{ $asset->volume }}</td>
                    <td class="text-right">
                        {{ number_format($asset->harga, 0, ',', '.') }}
                    </td>
                    <td class="text-right font-weight-bold">
                        {{ number_format($asset->nilai_buku, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center" style="padding: 20px; font-style: italic; background-color: #fafafa;">
                        Tidak ada data rekaman aset yang memenuhi parameter filter saat ini.
                    </td>
                </tr>
            @endforelse

            {{-- SUMMARY TOTAL FINANSIAL --}}
            @if(count($assets) > 0)
                <tr class="bg-summary font-weight-bold">
                    <td colspan="2" class="text-center" style="font-size: 10px; border-top: 2px double #000;">TOTAL REKAPITULASI</td>
                    <td colspan="9" style="border-top: 2px double #000;">
                        Jumlah Item Record: {{ count($assets) }} Baris Data Aset Terdaftar
                    </td>
                    <td class="text-right" style="font-size: 11px; color: #1e4620; border-top: 2px double #000;">
                        Rp {{ number_format($totalHarga, 0, ',', '.') }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- KELOMPOK BLOK BAWAH: AMAN DARI POTONGAN HALAMAN KARENA PAGE-BREAK-INSIDE AVOID --}}
    <div class="bottom-section">
        
        {{-- LOKASI & TANGGAL SURAT --}}
        <div class="lokasi-tanggal">
            Bandung, {{ now()->locale('id')->isoFormat('D MMMM Y') }}
        </div>

       {{-- LEMBAR TANDA TANGAN DOUBLE BERBASIS OTOMATISASI CRYPTOGRAPHY --}}
<table class="ttd-table" style="width: 100%; margin-top: 50px; text-align: center; font-family: sans-serif;">
    <tr>
        {{-- Kiri: Kepala Departemen (Dibuat Oleh) --}}
        <td class="ttd-cell" style="width: 35%; vertical-align: top;">
            <div class="ttd-title" style="font-size: 14px; margin-bottom: 15px;"></div>
            
            <div class="ttd-space" style="height: 85px; text-align: center; vertical-align: middle;">
                @if(isset($qrDataKadept))
                    <img src="data:image/svg+xml;base64,{{ base64_encode(QrCode::size(75)->generate($qrDataKadept)) }}" alt="QR Kadept" style="border: 1px solid #ddd; padding: 3px; background: #fff;">
                @else
                    <div style="height: 75px;"></div>
                @endif
            </div>
            
            <div class="ttd-line" style="font-weight: bold; text-decoration: underline; font-size: 14px; margin-top: 5px;">
                {{ $kepalaDept }}
            </div>
            <div class="ttd-jabatan" style="font-size: 12px; color: #555; margin-top: 5px;">
                Kepala Departemen General Affair
            </div>
        </td>

        {{-- Tengah: Informasi Cetak Audit Sistem (SIMASET Log Tracker) --}}
        <td class="ttd-cell-qr" style="width: 30%; vertical-align: middle; text-align: center;">
            @if(isset($qrCodeSystem))
                <div class="qr-container" style="display: inline-block; padding: 5px; border: 1px dashed #2e7d32; background: #fec-light;">
                    <span style="font-size: 9px; display: block; color: #2e7d32; font-weight: bold; letter-spacing: 0.5px;">
                        AUTOMATED STAMP
                    </span>
                </div>
                <div style="font-size: 8px; color: #777; margin-top: 6px; line-height: 1.2;">
                    <span>Printed By: {{ $printedBy }}</span><br>
                    <span>Time: {{ $timestamp }}</span>
                </div>
            @endif
        </td>

        {{-- Kanan: Kepala Divisi (Mengetahui / Menyetujui) --}}
        <td class="ttd-cell" style="width: 35%; vertical-align: top;">
            <div class="ttd-title" style="font-size: 14px; margin-bottom: 15px;">Mengetahui / Menyetujui,</div>
            
            <div class="ttd-space" style="height: 85px; text-align: center; vertical-align: middle;">
                @if(isset($qrDataKadiv))
                    <img src="data:image/svg+xml;base64,{{ base64_encode(QrCode::size(75)->generate($qrDataKadiv)) }}" alt="QR Kadiv" style="border: 1px solid #ddd; padding: 3px; background: #fff;">
                @else
                    <div style="height: 75px;"></div>
                @endif
            </div>
            
            <div class="ttd-line" style="font-weight: bold; text-decoration: underline; font-size: 14px; margin-top: 5px;">
                {{ $kepalaDivisi }}
            </div>
            <div class="ttd-jabatan" style="font-size: 12px; color: #555; margin-top: 5px;">
                Kepala Divisi SAUH
            </div>
        </td>
    </tr>
</table>

        {{-- FOOTER STRIP HIJAU (DIPOSISIKAN RELATIF AGAR TIDAK SALING TABRAK KONTEN) --}}
        <div class="footer-container">
            <div class="footer-line"></div>
            <table class="footer-table">
                <tr>
                    <td class="footer-text" style="color: #4caf50; font-weight: bold;">
                        BADAN AMIL ZAKAT NASIONAL (BAZNAS) PROVINSI JAWA BARAT<br>
                        <span style="color: #666; font-weight: normal;">Jl. Soekarno Hatta No 458 Bandung | Telp. (022) 8731-5606 | E-mail: baznasprov.jabar@baznas.go.id</span>
                    </td>
                </tr>
            </table>
        </div>

    </div>

    {{-- AUTOMATIC WINDOW PRINT --}}
    <script>
        window.onload = function() {
            window.print();
        }
    </script>

</body>
</html>
