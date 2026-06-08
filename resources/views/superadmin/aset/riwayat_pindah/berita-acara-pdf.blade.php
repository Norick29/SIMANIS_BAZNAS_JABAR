<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>

body {
    font-family: 'Times New Roman', Times, serif;
    font-size: 12px;
    line-height: 1.5;
    margin: 0;
    padding: 20px;
    color: #000;
}

.header {
    text-align: center;
    margin-bottom: 10px;
}

.header-logo {
    width: 70px;
    height: 70px;
    margin: 0 auto 8px;
}

.header-logo img {
    width: 100%;
    height: 100%;
}

.header h2 {
    margin: 3px 0;
    font-size: 13px;
    font-weight: bold;
}

.header h3 {
    margin: 2px 0;
    font-size: 11px;
    font-weight: normal;
}

.header p {
    margin: 2px 0;
    font-size: 10px;
}

hr {
    border: none;
    border-top: 2px solid #000;
    margin: 8px 0;
}

.judul {
    text-align: center;
    margin: 15px 0 20px 0;
    font-weight: bold;
    font-size: 13px;
    text-decoration: underline;
}

.content {
    margin-bottom: 20px;
    line-height: 1.6;
}

.content-item {
    margin: 8px 0;
    font-size: 11px;
}

.content-item strong {
    display: inline-block;
    width: 140px;
}

.pernyataan {
    margin: 20px 0;
    font-size: 11px;
    text-align: justify;
    line-height: 1.6;
}

.lokasi-tanggal {
    text-align: right;
    margin: 25px 0 20px 0;
    font-size: 11px;
}

.mengetahui {
    text-align: center;
    margin: 30px 0 15px 0;
    font-size: 11px;
    font-weight: bold;
}

.ttd-container {
    display: flex;
    justify-content: space-around;
    margin-top: 15px;
    gap: 20px;
}

.ttd-col {
    text-align: center;
    flex: 1;
}

.ttd-qr {
    text-align: center;
    margin: 40px 0 15px 0;
}

.ttd-qr img {
    width: 70px;
    height: 70px;
    margin: 0 auto;
    border: 1px solid #ccc;
    padding: 3px;
}

.ttd-nama {
    border-top: 1px solid #000;
    padding-top: 5px;
    font-size: 11px;
    margin-top: 10px;
    min-height: 30px;
}

.footer {
    text-align: center;
    font-size: 9px;
    margin-top: 40px;
    border-top: 1px solid #000;
    padding-top: 10px;
    color: #666;
}

</style>
</head>
<body>

{{-- HEADER DENGAN LOGO --}}
<div class="header">
    @if($logoBase64)
        <div class="header-logo">
            <img src="{{ $logoBase64 }}" alt="Logo BAZNAS">
        </div>
    @endif
    
    <h2>BADAN AMIL ZAKAT NASIONAL</h2>
    <h3>PROVINSI JAWA BARAT</h3>
    <p>Jl. Soekarno Hatta No. 458 Bandung | Telp. (022) 6731-6606</p>
</div>

<hr>

{{-- JUDUL DOKUMEN --}}
<div class="judul">
    BERITA ACARA PERPINDAHAN ASET
</div>

{{-- ISI KONTEN --}}
<div class="content">
    <div class="content-item">
        <strong>Kode Aset</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $pindah->asset->kode_aset ?? '-' }}
    </div>
    
    <div class="content-item">
        <strong>Nama Aset</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $pindah->asset->barang->nama_barang ?? '-' }}
    </div>
    
    <div class="content-item">
        <strong>Lokasi Awal</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $pindah->asset->lokasi->nama_lokasi ?? '-' }}
    </div>
    
    <div class="content-item">
        <strong>Lokasi Tujuan</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $pindah->lokasiBaru->nama_lokasi ?? '-' }}
    </div>
    
    <div class="content-item">
        <strong>PJ Awal</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $pindah->amilSebelum->nama ?? '-' }}
    </div>
    
    <div class="content-item">
        <strong>PJ Baru</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $pindah->amilSesudah->nama ?? '-' }}
    </div>
    
    <div class="content-item">
        <strong>Alasan</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: {{ $pindah->alasan ?? '-' }}
    </div>
</div>

{{-- PERNYATAAN --}}
<div class="pernyataan">
    Perpindahan aset ini telah disetujui melalui
    mekanisme persetujuan berjenjang pada Sistem
    Informasi Manajemen Aset BAZNAS Provinsi Jawa Barat.
</div>

{{-- LOKASI DAN TANGGAL --}}
<div class="lokasi-tanggal">
    Bandung, {{ now()->locale('id')->isoFormat('D MMMM Y') }}
</div>

{{-- MENGETAHUI --}}
<div class="mengetahui">
    Mengetahui,
</div>

{{-- TANDA TANGAN --}}
<div class="ttd-container">
    <div class="ttd-col">
        <strong style="font-size: 10px;">Kepala Departemen</strong>
        
        @if($qrKadept)
            <div class="ttd-qr">
                <img src="data:image/png;base64,{{ $qrKadept }}" alt="QR Code Kadept">
            </div>
        @else
            <div style="height: 80px;"></div>
        @endif
        
        <div class="ttd-nama">
            @if($kadeptSignature && $kadeptSignature->approvedBy)
                {{ $kadeptSignature->approvedBy->name }}
            @else
                .........................
            @endif
        </div>
    </div>
    
    <div class="ttd-col">
        <strong style="font-size: 10px;">Kepala Divisi</strong>
        
        @if($qrKadiv)
            <div class="ttd-qr">
                <img src="data:image/png;base64,{{ $qrKadiv }}" alt="QR Code Kadiv">
            </div>
        @else
            <div style="height: 80px;"></div>
        @endif
        
        <div class="ttd-nama">
            @if($kadivSignature && $kadivSignature->approvedBy)
                {{ $kadivSignature->approvedBy->name }}
            @else
                .........................
            @endif
        </div>
    </div>
</div>

{{-- FOOTER --}}
<div class="footer">
    <strong>BADAN AMIL ZAKAT NASIONAL (BAZNAS) PROVINSI JAWA BARAT</strong><br>
    Jl. Soekarno Hatta No. 458 Bandung | Telp. (022) 6731-6606 | Email: baznasjabarbiz@baznas.go.id<br>
    <br>
    Dokumen ini dibuat secara elektronik dan ditandatangani menggunakan Digital Signature.
</div>

</body>
</html>