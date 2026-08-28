<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan_Penyusutan_Peremajaan_BAZNAS_{{ date('d_M_Y') }}</title>
    <style>
        @page { size: A4; margin: 2.5cm 2cm 2.5cm 2cm; }
        body { font-family: 'Arial', sans-serif; font-size: 11px; line-height: 1.4; color: #000; margin: 0; padding: 0; }
        .kop-surat { text-align: center; margin-bottom: 10px; }
        .kop-logo { width: 80px; height: auto; margin: 0 auto 5px auto; }
        .kop-logo img { width: 100%; height: auto; }
        .kop-text-1 { font-size: 13px; font-weight: bold; margin: 0 0 2px 0; }
        .kop-text-2 { font-size: 11px; font-weight: bold; color: #444; margin: 0; }
        .line-separator { border: none; border-top: 1px solid #000; margin: 5px 0 15px 0; }
        .judul-dokumen { text-align: center; font-size: 12px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 0.5px; }
        .subtitle-laporan { text-align: center; font-size: 10.5px; margin-bottom: 20px; color: #444; font-style: italic; }
        .table-aset { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .table-aset th { border: 1px solid #000; padding: 6px 4px; font-size: 10px; font-weight: bold; background-color: #f2f2f2; text-align: center; text-transform: uppercase; }
        .table-aset td { border: 1px solid #000; padding: 6px 5px; font-size: 9.5px; vertical-align: middle; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-weight-bold { font-weight: bold; }
        .bg-summary { background-color: #e9ecef; }
        .bottom-section { page-break-inside: avoid; margin-top: 30px; }
        .lokasi-tanggal { text-align: center; margin-left: 50%; margin-bottom: 10px; font-size: 11px; }
        .ttd-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .ttd-cell { width: 33.33%; text-align: center; vertical-align: top; }
        .ttd-title { font-weight: bold; font-size: 11px; margin-bottom: 5px; }
        .ttd-space { height: 85px; margin: 5px 0; display: flex; align-items: center; justify-content: center; }
        .ttd-line { width: 160px; margin: 0 auto; border-bottom: 1px dashed #000; padding-top: 3px; font-weight: bold; font-size: 11px; }
        .ttd-jabatan { font-size: 10px; font-style: italic; margin-top: 4px; color: #555; }
        .footer-container { margin-top: 40px; width: 100%; }
        .footer-line { border: none; border-top: 1px solid #ccc; margin-bottom: 5px; }
        .footer-table { width: 100%; border-collapse: collapse; }
        .footer-text { width: 100%; font-size: 8px; color: #666; text-align: left; line-height: 1.3; }
        @media print { body { background: none; color: #000; } }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('image/logo-baznas.png');
        $logoSrc = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : asset('image/logo-baznas.png');
    @endphp
    <div class="kop-surat">
        <div class="kop-logo">
            <img src="{{ $logoSrc }}" alt="Logo BAZNAS">
        </div>
        <div class="kop-text-1">BADAN AMIL ZAKAT NASIONAL</div>
        <div class="kop-text-2">PROVINSI JAWA BARAT</div>
    </div>
    <div class="line-separator"></div>
    
    <div class="judul-dokumen">
        LAPORAN JURNAL AKUMULASI PENYUSUTAN & PEREMAJAAN ASET
    </div>
    <div class="subtitle-laporan">
        Dicetak pada: {{ now()->locale('id')->isoFormat('D MMMM Y, HH:mm') }} WIB
    </div>

    <table class="table-aset">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 15%;">Kode Aset</th>
                <th style="width: 20%;">Nama Barang</th>
                <th style="width: 13%;">Nilai Awal (Rp)</th>
                <th style="width: 12%;">Penyusutan (Rp)</th>
                <th style="width: 13%;">Nilai Akhir (Rp)</th>
                <th style="width: 15%;">Catatan Hasil Audit</th>
                <th style="width: 8%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dataPenyusutan as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center font-weight-bold">{{ $item->asset->kode_aset ?? '-' }}</td>
                    <td class="text-left">
                        <strong>{{ $item->asset->barang->nama_barang ?? '-' }}</strong><br>
                        <small style="color:#555;">Tahun Perolehan: {{ $item->asset->tahun ?? '-' }}</small>
                    </td>
                    <td class="text-right">{{ number_format($item->nilai_awal, 0, ',', '.') }}</td>
                    <td class="text-right text-danger">-{{ number_format($item->nilai_penyusutan, 0, ',', '.') }}</td>
                    <td class="text-right font-weight-bold">
                        @if($item->status == 'disetujui' && \Illuminate\Support\Str::contains(strtolower($item->alasan ?? ''), 'peremajaan'))
                            {{ number_format($item->nilai_setelah_penyusutan + ($item->nilai_penyusutan * 0.50), 0, ',', '.') }}
                        @else
                            {{ number_format($item->nilai_setelah_penyusutan, 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="text-left" style="font-size: 8.5px; max-width: 150px;">{{ $item->alasan ?? '-' }}</td>
                    <td class="text-center font-weight-bold" style="text-transform: uppercase; font-size: 8.5px;">
                        {{ $item->status }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; font-style: italic;">
                        Tidak ada rekaman data penyusutan atau peremajaan.
                    </td>
                </tr>
            @endforelse
            
            @if(count($dataPenyusutan) > 0)
                <tr class="bg-summary font-weight-bold">
                    <td colspan="3" class="text-center">TOTAL EVALUASI AUDIT FINANSIAL</td>
                    <td class="text-right">Rp {{ number_format($dataPenyusutan->sum('nilai_awal'), 0, ',', '.') }}</td>
                    <td class="text-right text-danger">Rp {{ number_format($dataPenyusutan->sum('nilai_penyusutan'), 0, ',', '.') }}</td>
                    <td class="text-right text-success" colspan="3">
                        Total Record: {{ count($dataPenyusutan) }} Item Berjalan
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="bottom-section">
        <div class="lokasi-tanggal">
            Bandung, {{ now()->locale('id')->isoFormat('D MMMM Y') }}
        </div>
        
        {{-- KOTAK TANDA TANGAN 3 KOLOM DINAMIS --}}
        <table class="ttd-table">
            <tr>
                {{-- KOLOM 1: KADEPT --}}
                <td class="ttd-cell">
                    <div class="ttd-title">Dibuat / Diperiksa Oleh,</div>
                    <div class="ttd-space">
                        @if(isset($qrDataKadept))
                            <img src="data:image/svg+xml;base64,{{ base64_encode(QrCode::size(70)->generate($qrDataKadept)) }}" alt="QR Kadept" style="border: 1px solid #ddd; padding: 2px;">
                        @endif
                    </div>
                    <div class="ttd-line">{{ $kepalaDept }}</div>
                    <div class="ttd-jabatan">Kepala Departemen GA</div>
                </td>
                
                {{-- KOLOM 2: SYSTEM TRACKER STAMP --}}
                <td class="ttd-cell" style="vertical-align: middle;">
                    @if(isset($qrCodeSystem))
                        <div style="margin-bottom: 5px;">
                            <img src="data:image/svg+xml;base64,{{ base64_encode(QrCode::size(55)->color(46, 125, 50)->generate($qrCodeSystem)) }}" alt="QR System" style="border: 1px dashed #2e7d32; padding: 3px; background: #f0fdf4;">
                        </div>
                        <div style="font-size: 8px; color: #2e7d32; font-weight: bold; margin-bottom: 2px;">AUTOMATED STAMP</div>
                        <div style="font-size: 7.5px; color: #777; line-height: 1.2;">
                            Printed By: {{ $printedBy }}<br>
                            Time: {{ $timestamp }}
                        </div>
                    @endif
                </td>

                {{-- KOLOM 3: KADIV --}}
                <td class="ttd-cell">
                    <div class="ttd-title">Mengetahui / Menyetujui,</div>
                    <div class="ttd-space">
                        @if(isset($qrDataKadiv))
                            <img src="data:image/svg+xml;base64,{{ base64_encode(QrCode::size(70)->generate($qrDataKadiv)) }}" alt="QR Kadiv" style="border: 1px solid #ddd; padding: 2px;">
                        @endif
                    </div>
                    <div class="ttd-line">{{ $kepalaDivisi }}</div>
                    <div class="ttd-jabatan">Kepala Divisi SAUH</div>
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

    <script>
        window.onload = function() { window.print(); }
    </script>
</body>
</html>
