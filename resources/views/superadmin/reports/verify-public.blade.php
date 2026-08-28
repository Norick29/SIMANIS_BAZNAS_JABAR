<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Laporan Digital</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-success text-white text-center py-4">
                    <i class="fas fa-shield-alt fa-3x mb-2"></i>
                    <h4 class="font-weight-bold mb-0">LAPORAN DIGITAL VALID</h4>
                    <small>Verifikasi dokumen laporan SIMASET</small>
                </div>
                <div class="card-body">
                    <p class="text-muted text-center mb-4">
                        Link verifikasi ini valid dan diterbitkan oleh sistem.
                    </p>
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th width="38%">Jenis Dokumen</th>
                            <td>{{ $document }}</td>
                        </tr>
                        <tr>
                            <th>Jabatan TTD</th>
                            <td><span class="badge badge-primary">{{ $role }}</span></td>
                        </tr>
                        <tr>
                            <th>Penandatangan</th>
                            <td><strong>{{ $signer }}</strong></td>
                        </tr>
                        <tr>
                            <th>Dicetak Oleh</th>
                            <td>{{ $printedBy }}</td>
                        </tr>
                        <tr>
                            <th>Waktu Cetak</th>
                            <td>{{ $printedAt }}</td>
                        </tr>
                        <tr>
                            <th>Key Fingerprint</th>
                            <td><code>{{ $fingerprint }}</code></td>
                        </tr>
                        <tr>
                            <th>Signature</th>
                            <td><code>{{ $signature }}</code></td>
                        </tr>
                    </table>
                    <div class="alert alert-info text-center mt-3 mb-0">
                        <i class="fas fa-info-circle mr-1"></i>
                        Integritas URL dijamin oleh signed route Laravel.
                    </div>
                </div>
                <div class="card-footer bg-white text-center text-muted">
                    <small>&copy; {{ date('Y') }} - IT Manajemen Aset</small>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
