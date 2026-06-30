<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Digital Dokumen BAZNAS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-success text-white py-4">
                        <i class="fas fa-check-circle fa-3x mb-2"></i>
                        <h4>DOKUMEN TERVERIFIKASI ASLI</h4>
                        <p class="mb-0 small">Sistem Informasi Manajemen Aset BAZNAS Prov. Jawa Barat</p>
                    </div>
                    <div class="card-body text-left">
                        <p class="text-muted text-center small">Integrasi Keamanan Valid: <strong> ✓ URL Digital Signature berhasil diverifikasi</strong></p>
                        <hr>
                        <table class="table table-sm table-borderless">
                            <tr><th>Kode Aset</th><td>: <strong>{{ $hilang->asset->kode_aset }}</strong></td></tr>
                            <tr><th>Nama Barang</th><td>: {{ $hilang->asset->barang->nama_barang ?? '-' }}</td></tr>
                            <tr><th>Lokasi Terakhir</th><td>: {{ $hilang->asset->lokasi->nama_lokasi ?? '-' }}</td></tr>
                            <tr><th>Amil Utama</th><td>: {{ $hilang->asset->amil->nama ?? '-' }}</td></tr>
                            <tr><th>Tanggal Hilang</th><td>: {{ \Carbon\Carbon::parse($hilang->tanggal_hilang)->format('d-m-Y') }}</td></tr>
                            <tr><th>Status Log</th><td>: <span class="badge badge-danger">Tercatat Hilang</span></td></tr>
                        </table>
                        <hr>
                        <p class="small text-muted text-center mb-0">Dokumen Digital ini di-generate secara sah oleh sistem dan tidak dapat dimanipulasi secara ilegal.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>