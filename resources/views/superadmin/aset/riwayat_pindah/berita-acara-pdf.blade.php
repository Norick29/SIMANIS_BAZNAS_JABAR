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
    width: 100px;
}

/* GAYA TABEL FISIK (PERSIS SEPERTI DI FOTO) */
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
    background-color: #ffffff;
    text-align: center;
}
.table-aset td {
    border: 1px solid #000;
    padding: 6px 5px;
    font-size: 10.5px;
    vertical-align: middle;
}
.text-center { text-align: center; }
.text-left { text-align: left; }

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
    height: 75px;
    margin: 5px 0;
}
.ttd-space img {
    width: 70px;
    height: 70px;
}
.ttd-line {
    width: 180px;
    margin: 0 auto;
    border-bottom: 1px dashed #000;
    padding-top: 3px;
}
.ttd-jabatan {
    font-size: 10.5px;
    font-style: italic;
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

{{-- JUDUL BERITA ACARA PERPINDAHAN --}}
<div class="judul-dokumen">
    BERITA ACARA PERPINDAHAN ASET<br>
    BAZNAS PROVINSI JAWA BARAT
</div>

<div class="section-pihak">
    Yang bertanda tangan di bawah ini:
    
    {{-- PIHAK PERTAMA (PJ AWAL) --}}
    <div class="pihak-container">
        <strong>1. Nama</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $pindah->amilSebelum->nama ?? '-' }}<br>
        <div class="pihak-row"><span class="label-pihak">Jabatan</span>: Penanggung Jawab Aset (Lama)</div>
        Yang selanjutnya disebut <strong>PIHAK PERTAMA</strong>
    </div>

    {{-- PIHAK KEDUA (PJ BARU / STAGE 2 APPROVER) --}}
    <div class="pihak-container">
        <strong>2. Nama</strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $pindah->amilSesudah->nama ?? '-' }}<br>
        <div class="pihak-row"><span class="label-pihak">Jabatan</span>: Penanggung Jawab Aset (Baru)</div>
        Yang selanjutnya disebut <strong>PIHAK KEDUA</strong>
    </div>
</div>

<div class="narasi-penutup">
    <strong>PIHAK PERTAMA</strong> menyerahkan tanggung jawab pengelolaan aset/barang inventaris milik BAZNAS Provinsi Jawa Barat kepada <strong>PIHAK KEDUA</strong>, dan <strong>PIHAK KEDUA</strong> menerima penyerahan tersebut dalam keadaan baik dengan rincian sebagai berikut:
</div>

{{-- TABEL RAPI SAMA PERSIS SEPERTI DI GAMBAR --}}
<table class="table-aset">
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 35%;">Nama Barang/Aset</th>
            <th style="width: 25%;">Kode Aset</th>
            <th style="width: 10%;">Jumlah</th>
            <th style="width: 25%;">Lokasi Tujuan</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-center">1</td>
            <td class="text-left">{{ $pindah->asset->barang->nama_barang ?? '-' }}</td>
            <td class="text-center">{{ $pindah->asset->kode_aset ?? '-' }}</td>
            <td class="text-center">1 Unit</td>
            <td class="text-center">{{ $pindah->lokasiBaru->nama_lokasi ?? '-' }}</td>
        </tr>
    </tbody>
</table>

<div class="narasi-penutup">
    Dengan diterbitkannya Berita Acara ini, maka hak penempatan dan tanggung jawab pemeliharaan aset tersebut kini beralih sepenuhnya dari PIHAK PERTAMA kepada PIHAK KEDUA.
</div>

{{-- TANGGAL SURAT --}}
<div class="lokasi-tanggal">
    Bandung, {{ now()->locale('id')->isoFormat('D MMMM Y') }}
</div>

{{-- STRUKTUR TANDA TANGAN DISESUAIKAN DATA CONTROLLER (KADEPT DAN KADIV) --}}
<table class="table-ttd">
    <tr>
        {{-- KEPALA DEPARTEMEN (VERIFIKATOR STAGE 1) --}}
        <td class="ttd-cell">
            <div class="ttd-title">PIHAK PERTAMA</div>
            <div class="ttd-space">
                @if($qrDataKadept)
                    <img src="data:image/svg+xml;base64,{{ base64_encode(QrCode::size(80)->generate($qrDataKadept)) }}" alt="QR Kadept">
                @else
                    <div style="height: 70px;"></div>
                @endif
            </div>
            <div class="ttd-line">
                {{ $kadeptSignature->approvedBy->name ?? '........................................' }}
            </div>
            <div class="ttd-jabatan">Kepala Departemen</div>
        </td>

        {{-- KEPALA DIVISI (APPROVER STAGE 2) --}}
        <td class="ttd-cell">
            <div class="ttd-title">PIHAK KEDUA</div>
            <div class="ttd-space">
                @if($qrDataKadiv)
                    <img src="data:image/svg+xml;base64,{{ base64_encode(QrCode::size(80)->generate($qrDataKadiv)) }}" alt="QR Kadiv">
                @else
                    <div style="height: 70px;"></div>
                @endif
            </div>
            <div class="ttd-line">
                {{ $kadivSignature->approvedBy->name ?? '........................................' }}
            </div>
            <div class="ttd-jabatan">Kepala Divisi</div>
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
                {{-- Bagian ISO sertifikasi jika kamu punya gambarnya di public/image/iso.png silakan aktifkan tag ini --}}
                {{-- <img src="{{ public_path('image/iso.png') }}" alt="ISO"> --}}
            </td>
        </tr>
    </table>
</div>

</body>
</html>