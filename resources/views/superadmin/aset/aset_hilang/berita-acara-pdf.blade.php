<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body {
    font-family: 'Arial', sans-serif;
    font-size: 11px;
    line-height: 1.4;
    margin: 0;
    padding: 10px 20px;
    color: #000;
}

/* KOP SURAT ATAS TENGAH */
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
    margin-bottom: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* PIHAK YANG BERTANDATANGAN */
.section-pihak {
    margin-bottom: 15px;
    text-align: justify;
}
.pihak-container {
    margin-left: 15px;
    margin-bottom: 10px;
}
.pihak-row {
    margin: 3px 0;
}
.label-pihak {
    display: inline-block;
    width: 130px;
}

/* TABEL INFORMASI KRONOLOGI */
.table-aset {
    width: 100%;
    border-collapse: collapse;
    margin: 15px 0;
}
.table-aset th {
    border: 1px solid #000;
    padding: 6px 4px;
    font-size: 11px;
    font-weight: bold;
    background-color: #f2f2f2;
    text-align: center;
}
.table-aset td {
    border: 1px solid #000;
    padding: 6px 5px;
    font-size: 10.5px;
    vertical-align: top;
}
.text-center { text-align: center; }
.text-left { text-align: left; }

.kronologi-box {
    border: 1px solid #000;
    padding: 10px;
    margin: 10px 0;
    text-align: justify;
    background-color: #fafafa;
}

.narasi-penutup {
    margin: 15px 0;
    text-align: justify;
}

.lokasi-tanggal {
    text-align: center;
    margin-left: 50%;
    margin-bottom: 15px;
    font-size: 11px;
}

/* TABEL TANDA TANGAN KANAN KIRI */
.table-ttd {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
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
    height: 130px;
}

.ttd-space img {
    width: 120px;
    height: 120px;
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
    margin-top: 2px;
}

/* FOOTER ASLI BAZNAS */
.footer-container {
    position: absolute;
    bottom: -10px;
    left: 0;
    right: 0;
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
    width: 80%;
    font-size: 8px;
    color: #666;
    text-align: left;
    line-height: 1.3;
}
.footer-logo-iso {
    width: 20%;
    text-align: right;
}
.footer-logo-iso img {
    height: 25px;
    width: auto;
}
</style>
</head>
<body>

{{-- KOP SURAT MODEL ATAS-TENGAH --}}
<div class="kop-surat">
    <div class="kop-logo">
        <img src="{{ public_path('image/logo-baznas.png') }}" alt="Logo BAZNAS">
    </div>
    <div class="kop-text-1">BADAN AMIL ZAKAT NASIONAL</div>
    <div class="kop-text-2">PROVINSI JAWA BARAT</div>
</div>

<div class="line-separator"></div>

{{-- JUDUL BERITA ACARA --}}
<div class="judul-dokumen">
    BERITA ACARA KRONOLOGI KEHILANGAN ASET<br>
    BAZNAS PROVINSI JAWA BARAT
</div>

<div class="section-pihak">
    Dokumen audit internal ini menerangkan bahwa rincian pelaporan di bawah ini melibatkan pihak-pihak operasional berikut:
    
    {{-- PIHAK PERTAMA (STAF PELAPOR) --}}
    <div class="pihak-container">
        <strong>1. Nama</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $hilang->user->name ?? '-' }}<br>
        <div class="pihak-row"><span class="label-pihak">Jabatan / Role</span>: Staf Operasional (Pelapor Sistem)</div>
        Yang selanjutnya disebut sebagai <strong>PIHAK PELAPOR</strong>.
    </div>

    {{-- PIHAK KEDUA (AMIL PENANGGUNG JAWAB) --}}
    <div class="pihak-container">
        <strong>2. Nama</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $hilang->asset->amil->nama ?? '-' }}<br>
        <div class="pihak-row"><span class="label-pihak">Penanggung Jawab Aset</span>: Amil Inventaris Master</div>
        Yang selanjutnya disebut sebagai <strong>AMIL UTAMA</strong>.
    </div>
</div>

<div class="narasi-penutup">
    Berdasarkan laporan fisik manajemen inventaris, telah tercatat insiden kehilangan terhadap 1 (satu) unit aset milik BAZNAS Provinsi Jawa Barat dengan rincian identitas spesifik sebagai berikut:
</div>

{{-- TABEL DATA RAPI ASET --}}
<table class="table-aset">
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 35%;">Nama Barang / Merk</th>
            <th style="width: 25%;">Kode Aset</th>
            <th style="width: 15%;">Lokasi Terakhir</th>
            <th style="width: 20%;">Estimasi Waktu Hilang</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-center">1</td>
            <td class="text-left">
                <strong>{{ $hilang->asset->barang->nama_barang ?? '-' }}</strong><br>
                <small>Merk: {{ $hilang->asset->merk ?? '-' }}</small>
            </td>
            <td class="text-center" style="font-weight: bold; color: #d9534f;">{{ $hilang->asset->kode_aset ?? '-' }}</td>
            <td class="text-center">{{ $hilang->asset->lokasi->nama_lokasi ?? '-' }}</td>
            <td class="text-center">{{ \Carbon\Carbon::parse($hilang->tanggal_hilang)->locale('id')->isoFormat('D MMMM Y') }}</td>
        </tr>
    </tbody>
</table>

{{-- KRONOLOGI INSIDEN --}}
<div class="narasi-penutup">
    <strong>Kronologi Kejadian Kehilangan:</strong>
</div>
<div class="kronologi-box">
    {!! nl2br(e($hilang->kronologi)) !!}
</div>

<div class="narasi-penutup">
    Demikian Berita Acara Kronologi ini dibuat secara sadar dan sah melalui sistem verifikasi digital untuk digunakan sebagai berkas otentik dalam keperluan audit fisik operasional serta dasar peninjauan status pembukuan inventaris lembaga.
</div>

{{-- TANGGAL SURAT --}}
<div class="lokasi-tanggal">
    Bandung, {{ $hilang->created_at->locale('id')->isoFormat('D MMMM Y') }}
</div>

{{-- STRUKTUR TANDA TANGAN VERIFIKASI DIGITAL (SHA-256 SIGNED ROUTE) --}}
<table class="table-ttd">
    <tr>
        {{-- STAF PELAPOR --}}
        <td class="ttd-cell">
            <div class="ttd-title">PIHAK PELAPOR</div>
            <div class="ttd-space">
                @if($qrDataPelapor)
                    {{-- Generate QR Code dengan integrasi tautan SHA-256 --}}
                    <img src="data:image/svg+xml;base64,{{ base64_encode(
                        QrCode::size(180)
                            ->margin(1)
                            ->generate($qrDataPelapor)
                    ) }}" alt="QR Pelapor">
                @else
                    <div style="height: 70px;"></div>
                @endif
            </div>
            <div class="ttd-line">
                {{ $hilang->user->name ?? '........................................' }}
            </div>
            <div class="ttd-jabatan">Staf Pelapor Dokumen</div>
        </td>

        {{-- AMIL UTAMA --}}
        <td class="ttd-cell">
            <div class="ttd-title">AMIL UTAMA</div>
            <div class="ttd-space">
                @if($qrDataAmil)
                    <img src="data:image/svg+xml;base64,{{ base64_encode(
                        QrCode::size(180)
                            ->margin(1)
                            ->generate($qrDataAmil)
                    ) }}" alt="QR Amil">
                @else
                    <div style="height: 70px;"></div>
                @endif
            </div>
            <div class="ttd-line">
                {{ $hilang->asset->amil->nama ?? '........................................' }}
            </div>
            <div class="ttd-jabatan">Penanggung Jawab Aset</div>
        </td>
    </tr>
</table>

{{-- FOOTER STRIP HIJAU ASLI BAZNAS PROV JABAR --}}
<div class="footer-container">
    <div class="footer-line"></div>
    <table class="footer-table">
        <tr>
            <td class="footer-text" style="color: #4caf50; font-weight: bold;">
                BADAN AMIL ZAKAT NASIONAL (BAZNAS) PROVINSI JAWA BARAT<br>
                <span style="color: #666; font-weight: normal;">Jl. Soekarno Hatta No 458 Bandung | Telp. (022) 8731-5606 | E-mail: baznasprov.jabar@baznas.go.id</span>
            </td>
            <td class="footer-logo-iso">
                {{-- Uncomment baris di bawah jika logo sertifikasi ISO mau dimunculkan --}}
                {{-- <img src="{{ public_path('image/iso.png') }}" alt="ISO"> --}}
            </td>
        </tr>
    </table>
</div>

</body>
</html>