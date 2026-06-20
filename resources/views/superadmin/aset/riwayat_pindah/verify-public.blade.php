<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Digital Signature</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-success text-white text-center py-4">
                    <i class="fas fa-shield-check fa-3x mb-2"></i>
                    <h4 class="font-weight-bold mb-0">TANDA TANGAN DIGITAL VALID</h4>
                    <small>Sistem Manajemen Aset Berita Acara</small>
                </div>
                <div class="card-body">
                    <p class="text-muted text-center mb-4">Dokumen ini telah ditandatangani secara digital via sistem dan dinyatakan sah.</p>
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>Status Jabatan</th>
                            <td><span class="badge badge-primary">{{ $approval->stage == 1 ? 'Kepala Departemen' : 'Kepala Divisi' }}</span></td>
                        </tr>
                        <tr>
                            <th>Penandatangan</th>
                            <td><strong>{{ $approval->approvedBy->name ?? 'N/A' }}</strong></td>
                        </tr>
                        <tr>
                            <th>Waktu Approval</th>
                            <td>{{ $approval->approved_at ? \Carbon\Carbon::parse($approval->approved_at)->translatedFormat('d F Y H:i:s') : '-' }}</td>
                        </tr>
                    </table>
                    <div class="alert alert-info text-center mt-3 mb-0">
                        <i class="fas fa-info-circle mr-1"></i> Integritas dokumen dijamin oleh sistem enkripsi RSA internal.
                    </div>
                </div>
                <div class="card-footer bg-white text-center text-muted">
                    <small>© {{ date('Y') }} - IT Manajemen Aset</small>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>