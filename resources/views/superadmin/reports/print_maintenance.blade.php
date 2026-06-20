<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan_Maintenance_Aset_BAZNAS_{{ date('d_M_Y') }}</title>
    <style>
        @page {
            size: A4;
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
            font-size: 10.5px;
            font-weight: bold;
            background-color: #f2f2f2;
            text-align: center;
            text-transform: uppercase;
        }
        .table-aset td {
            border: 1px solid #000;
            padding: 6px 5px;
            font-size: 10px;
            vertical-align: middle;
        }
        
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-weight-bold { font-weight: bold; }
        .bg-summary { background-color: #e9ecef; }

        /* REKAYASA STRUKTUR BAWAH (TTD & FOOTER) */
        .bottom-section {
            page-break-inside: avoid; 
            margin-top: 30px;
            margin-bottom: 80px; 
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

        /* FOOTER DINAMIS DI DASAR KERTAS */
        .footer-container {
            position: fixed;
            bottom: -1cm;
            left: 0;
            right: 0;
            height: 40px;
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

        @media print {
            body {
                background: none;
                color: #000;
            }
            .footer-container {
                position: fixed;
                bottom: -1cm;
            }
        }
    </style>
</head>
<body>

    {{-- KOP SURAT MODEL ATAS-TENGAH BAWAAN BAZNAS --}}
    <div class="kop-surat">
        <div class="kop-logo">
            <img src="{{ asset('image/logo-baznas.png') }}" alt="Logo BAZNAS">
        </div>
        <div class="kop-text-1">BADAN AMIL ZAKAT NASIONAL</div>
        <div class="kop-text-2">PROVINSI JAWA BARAT</div>
    </div>

    <div class="line-separator"></div>

    {{-- JUDUL DOKUMEN REKAPITULASI --}}
    <div class="judul-dokumen">
        LAPORAN REKAPITULASI BIAYA & PERBAIKAN (MAINTENANCE) ASET
    </div>
    <div class="subtitle-laporan">
        Parameter Filter: [ {{ $stringFilter }} ]
    </div>

    {{-- TABEL DATA BERGAYA AUDIT FISIK --}}
    <table class="table-aset">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 12%;">Tgl Mulai</th>
                <th style="width: 18%;">Kode Aset</th>
                <th style="width: 18%;">Nama Aset / Ruangan</th>
                <th style="width: 12%;">Jenis Servis</th>
                <th style="width: 18%;">Deskripsi Kerusakan</th>
                <th style="width: 6%;">Status</th>
                <th style="width: 12%;">Biaya Perbaikan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($maintenances as $index => $mt)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">
                        {{ $mt->tanggal_mulai ? $mt->tanggal_mulai->format('d/m/Y') : '-' }}
                    </td>
                    <td class="text-center font-weight-bold">{{ $mt->asset->kode_aset ?? '-' }}</td>
                    <td class="text-left">
                        <strong>{{ $mt->asset->barang->nama_barang ?? '-' }}</strong><br>
                        <small style="color:#555;">Ruang: {{ $mt->asset->lokasi->nama_lokasi ?? '-' }}</small>
                    </td>
                    <td class="text-center" style="text-transform: capitalize;">{{ $mt->jenis }}</td>
                    <td class="text-left">{{ $mt->deskripsi }}</td>
                    <td class="text-center font-weight-bold" style="text-transform: uppercase; font-size: 9px;">
                        {{ $mt->status }}
                    </td>
                    <td class="text-right">
                        {{ number_format($mt->biaya, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; font-style: italic; background-color: #fafafa;">
                        Tidak ada catatan riwayat perbaikan (maintenance) yang memenuhi parameter filter.
                    </td>
                </tr>
            @endforelse

            {{-- SUMMARY TOTAL BIAYA OPERASIONAL MAINTENANCE --}}
            @if(count($maintenances) > 0)
                <tr class="bg-summary font-weight-bold">
                    <td colspan="2" class="text-center" style="font-size: 10px; border-top: 2px double #000;">TOTAL BIAYA</td>
                    <td colspan="5" style="border-top: 2px double #000;">
                        Jumlah Kasus Kerusakan: {{ count($maintenances) }} Aset Telah Ditangani
                    </td>
                    <td class="text-right" style="font-size: 11px; color: #b22222; border-top: 2px double #000;">
                        Rp {{ number_format($totalBiaya, 0, ',', '.') }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- KELOMPOK BLOK BAWAH (TTD & FOOTER) --}}
    <div class="bottom-section">
        
        <div class="lokasi-tanggal">
            Bandung, {{ now()->locale('id')->isoFormat('D MMMM Y') }}
        </div>

         <table class="ttd-table" style="width: 100%; margin-top: 50px; text-align: center; font-family: sans-serif;">
            <tr>
                <td class="ttd-cell" style="width: 35%; vertical-align: top;">
                    <div class="ttd-title" style="font-size: 14px; margin-bottom: 15px;">Dibuat Oleh,</div>
                    
                    <div class="ttd-space" style="height: 70px;"></div>
                    
                    <div class="ttd-line" style="font-weight: bold; text-decoration: underline; font-size: 14px;">
                        {{ $kepalaDept }}
                    </div>
                    <div class="ttd-jabatan" style="font-size: 12px; color: #555; margin-top: 5px;">
                        Kepala Departemen General Affair
                    </div>
                </td>

                <td class="ttd-cell-qr" style="width: 30%; vertical-align: middle; text-align: center;">
                    @if(isset($qrCodeSystem))
                        <div class="qr-container" style="display: inline-block; padding: 5px; border: 1px solid #ddd; background: #fff;">
                            {{-- Eksekusi generator QR Code bawaan Laravel kamu di sini, contoh: --}}
                            {{-- {!! QrCode::size(80)->generate($qrCodeSystem) !!} --}}
                            <span style="font-size: 10px; display: block; color: #2e7d32; font-weight: bold; margin-top: 2px;">
                                SYSTEM VERIFIED
                            </span>
                        </div>
                    @endif
                </td>

                <td class="ttd-cell" style="width: 35%; vertical-align: top;">
                    <div class="ttd-title" style="font-size: 14px; margin-bottom: 15px;">Mengetahui / Menyetujui,</div>
                    
                    <div class="ttd-space" style="height: 70px;"></div>
                    
                    <div class="ttd-line" style="font-weight: bold; text-decoration: underline; font-size: 14px;">
                        {{ $kepalaDivisi }}
                    </div>
                    <div class="ttd-jabatan" style="font-size: 12px; color: #555; margin-top: 5px;">
                        Kepala Divisi SAUH
                    </div>
                </td>
            </tr>
        </table>

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