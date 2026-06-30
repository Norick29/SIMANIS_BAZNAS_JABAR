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
/* GAYA TABEL FISIK */
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
.ttd-line-nama {
    font-weight: bold;
    font-size: 11px;
    margin-top: 5px;
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

{{-- JUDUL BERITA ACARA PENGHAPUSAN --}}
<div class="judul-dokumen">
    BERITA ACARA PENGHAPUSAN & PEMUSNAHAN ASET<br>
    BAZNAS PROVINSI JAWA BARAT
</div>

<div class="section-pihak">
    Berdasarkan hasil verifikasi kondisi fisik serta persetujuan berjenjang manajemen, yang bertanda tangan di bawah ini menyatakan bahwa aset/barang inventaris milik BAZNAS Provinsi Jawa Barat yang tertera di bawah ini telah disetujui untuk dinonaktifkan dari sistem sirkulasi operasional utama:
</div>

{{-- TABEL DATA ASET YANG DIHAPUS --}}
<table class="table-aset">
    <thead>
        <tr>
            <th style="width: 5%;">No</th>
            <th style="width: 30%;">Nama Barang/Aset</th>
            <th style="width: 25%;">Kode Aset</th>
            <th style="width: 20%;">Lokasi Terakhir</th>
            <th style="width: 20%;">Penanggung Jawab</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-center">1</td>
            <td class="text-left">{{ $penghapusan->asset->barang->nama_barang ?? '-' }}</td>
            <td class="text-center">{{ $penghapusan->asset->kode_aset ?? '-' }}</td>
            <td class="text-center">{{ $penghapusan->asset->lokasi->nama_lokasi ?? '-' }}</td>
            <td class="text-center">{{ $penghapusan->asset->amil->nama ?? '-' }}</td>
        </tr>
    </tbody>
</table>

<h5 style="margin-bottom: 5px; font-size: 11px; font-weight: bold;">Alasan / Pertimbangan Penghapusan:</h5>
<div style="padding: 8px; background-color: #f9f9f9; border: 1px solid #ddd; text-align: justify; font-style: italic; margin-bottom: 15px;">
    "{{ $penghapusan->alasan }}"
</div>

<div class="narasi-penutup">
    Demikian Berita Acara ini dibuat dengan sebenar-benarnya agar dapat digunakan sebagaimana mestinya sebagai bukti mutasi arsip status aset, pemutakhiran buku inventaris, serta laporan pertanggungjawaban divisi logistik BAZNAS Provinsi Jawa Barat.
</div>

{{-- TANGGAL SURAT --}}
<div class="lokasi-tanggal">
    Bandung, {{ \Carbon\Carbon::parse($penghapusan->created_at)->locale('id')->isoFormat('D MMMM Y') }}
</div>

{{-- STRUKTUR TANDA TANGAN DIGITAL --}}
<table class="table-ttd">
    <tr>
        {{-- KEPALA DEPARTEMEN (STAGE 1) --}}
        <td class="ttd-cell">
            <div class="ttd-title">Mengetahui,</div>
            <div class="ttd-space">
                @if($qrDataKadept)
                    <img src="data:image/svg+xml;base64,{{ base64_encode(QrCode::size(70)->generate($qrDataKadept)) }}" alt="QR Kadept">
                @else
                    <div style="height: 70px;"></div>
                @endif
            </div>
            <div class="ttd-line"></div>
            <div class="ttd-line-nama">{{ $kadeptSignature->approvedBy->name ?? '........................................' }}</div>
            <div class="ttd-jabatan">Kepala Departemen</div>
        </td>
        {{-- KEPALA DIVISI (STAGE 2) --}}
        <td class="ttd-cell">
            <div class="ttd-title">Menyetujui,</div>
            <div class="ttd-space">
                @if($qrDataKadiv)
                    <img src="data:image/svg+xml;base64,{{ base64_encode(QrCode::size(70)->generate($qrDataKadiv)) }}" alt="QR Kadiv">
                @else
                    <div style="height: 70px;"></div>
                @endif
            </div>
            <div class="ttd-line"></div>
            <div class="ttd-line-nama">{{ $kadivSignature->approvedBy->name ?? '........................................' }}</div>
            <div class="ttd-jabatan">Kepala Divisi</div>
        </td>
    </tr>
</table>

{{-- FOOTER STRIP HIJAU BAZNAS JABAR --}}
<div class="footer-container">
    <div class="footer-line"></div>
    <table class="footer-table">
        <tr>
            <td class="footer-text" style="color: #4caf50; font-weight: bold;">
                BADAN AMIL ZAKAT NASIONAL (BAZNAS) PROVINSI JAWA BARAT<br>
                <span style="color: #666; font-weight: normal;">Jl. Soekarno Hatta No 458 Bandung | Telp. (022) 8731-5606 | E-mail: baznasprov.jabar@baznas.go.id</span>
            </td>
            <td class="footer-logo-iso"></td>
        </tr>
    </table>
</div>

</body>
</html>