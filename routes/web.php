<?php

use App\Http\Controllers\ProfileController;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AmilController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\JenisController;
use App\Http\Controllers\SumberController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AsetHilangController;
use App\Http\Controllers\PenyusutanAsetController;
use App\Http\Controllers\MasterPenyusutanController;
use App\Http\Controllers\PenghapusanAsetController;
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/amil', [AmilController::class, 'index'])->name('amil.index');
    Route::post('/amil', [AmilController::class, 'store'])->name('amil.store');
    Route::put('/amil/{id}', [AmilController::class, 'update'])->name('amil.update');
    Route::delete('/amil/{id}', [AmilController::class, 'destroy'])->name('amil.destroy');
    Route::post('/master/amil/import', [AmilController::class, 'import'])->name('amil.import');

    Route::get('/jabatan', [JabatanController::class, 'index'])->name('jabatan.index');
    Route::post('/jabatan', [JabatanController::class, 'store'])->name('jabatan.store');
    Route::put('/jabatan/{id}', [JabatanController::class, 'update'])->name('jabatan.update');
    Route::delete('/jabatan/{id}', [JabatanController::class, 'destroy'])->name('jabatan.destroy');

    Route::get('/divisi', [DivisiController::class, 'index'])->name('divisi.index');
    Route::post('/divisi', [DivisiController::class, 'store'])->name('divisi.store');
    Route::put('/divisi/{id}', [DivisiController::class, 'update'])->name('divisi.update');
    Route::delete('/divisi/{id}', [DivisiController::class, 'destroy'])->name('divisi.destroy');

    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::post('/kategori/store', [KategoriController::class, 'store'])->name('kategori.store');
    Route::put('/kategori/{id}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');

    Route::get('/lokasi', [LokasiController::class, 'index'])->name('lokasi.index');
    Route::post('/lokasi/store', [LokasiController::class, 'store'])->name('lokasi.store');
    Route::put('/lokasi/{id}', [LokasiController::class, 'update'])->name('lokasi.update');
    Route::delete('/lokasi/{id}', [LokasiController::class, 'destroy'])->name('lokasi.destroy');
    Route::post('/master/lokasi/import', [LokasiController::class, 'import'])->name('lokasi.import');

    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    Route::post('/barang/store', [BarangController::class, 'store'])->name('barang.store');
    Route::put('/barang/{id}', [BarangController::class, 'update'])->name('barang.update');
    Route::delete('/barang/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');
    Route::post('/master/barang/import', [BarangController::class, 'import'])->name('barang.import');

    Route::get('/jenis', [JenisController::class, 'index'])->name('jenis.index');
    Route::post('/jenis/store', [JenisController::class, 'store'])->name('jenis.store');
    Route::put('/jenis/{id}', [JenisController::class, 'update'])->name('jenis.update');
    Route::delete('/jenis/{id}', [JenisController::class, 'destroy'])->name('jenis.destroy');
    Route::post('/master/jenis/import', [JenisController::class, 'import'])->name('jenis.import');

    Route::get('/sumber', [SumberController::class, 'index'])->name('sumber.index');
    Route::post('/sumber/store', [SumberController::class, 'store'])->name('sumber.store');
    Route::put('/sumber/{id}', [SumberController::class, 'update'])->name('sumber.update');
    Route::delete('/sumber/{id}', [SumberController::class, 'destroy'])->name('sumber.destroy');

    Route::prefix('master-penyusutan')->group(function () {

    Route::get('/', [MasterPenyusutanController::class, 'index'])
        ->name('master-penyusutan.index');

    Route::post('/store', [MasterPenyusutanController::class, 'store'])
        ->name('master-penyusutan.store');

    Route::put('/update/{id}', [MasterPenyusutanController::class, 'update'])
        ->name('master-penyusutan.update');

    Route::delete('/destroy/{id}', [MasterPenyusutanController::class, 'destroy'])
        ->name('master-penyusutan.destroy');

});

    Route::get('/penghapusan-aset', [PenghapusanAsetController::class,'index'])->name('penghapusan.index');
    Route::get('/penghapusan-aset/{id}', [PenghapusanAsetController::class,'show'])->name('penghapusan.show');
    Route::get('/penghapusan-aset/cetak-berita-acara/{id}', [PenghapusanAsetController::class, 'cetakBeritaAcaraPdf'])->name('penghapusan.cetak-ba');

    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::post('/assets/store', [AssetController::class, 'store'])->name('assets.store');
    Route::get('/assets/bulk-print-qr', [AssetController::class, 'bulkPrintQr'])->name('assets.bulk-print-qr');
    Route::post('/assets/selected-print-qr',[AssetController::class, 'selectedPrintQr'])->name('assets.selected-print-qr');
    Route::put('/assets/{id}', [AssetController::class, 'update'])->name('assets.update');
    Route::post('assets/pindah', [AssetController::class, 'pindahAset'])->name('assets.pindah');
    Route::get('/assets/{id}', [AssetController::class, 'show'])->name('assets.show');
    Route::post('/assets/import', [AssetController::class, 'import'])->name('assets.import');
    Route::get('/assets/{id}/json', [AssetController::class, 'getJsonDetail'])->name('assets.json');
    Route::get('/assets/referensi-data', [AssetController::class, 'getReferensiData']);
    Route::get('/assets/referensi-pindah', [AssetController::class, 'getReferensiPindah']);
    Route::get('/riwayat-pindah-aset', [ApprovalController::class, 'riwayatPindah'])->name('riwayat.pindah');

    Route::prefix('aset-hilang')->group(function () {
    Route::get('/', [AsetHilangController::class, 'index'])->name('hilang.index');
    Route::post('/store', [AsetHilangController::class, 'store'])->name('hilang.store');
    Route::get('/show/{id}', [AsetHilangController::class, 'showBeritaAcara'])->name('hilang.berita-acara');
    Route::get('/cetak-berita-acara/{id}', [AsetHilangController::class, 'downloadBeritaAcaraPdf'])->name('hilang.cetak');

    Route::middleware(['auth'])->group(function () {
    // Route halaman utama rekap penyusutan
    Route::get('/aset-penyusutan', [PenyusutanAsetController::class, 'index'])->name('penyusutan.index');
    Route::get('/aset-penyusutan/cetak-berita-acara/{id}', [PenyusutanAsetController::class, 'cetakBaPeremajaanPdf'])->name('peremajaan.cetak-ba');

    // Route pengajuan tindakan audit pasca-penyusutan 5 tahun
    Route::post('/aset-penyusutan/peremajaan/{id}', [PenyusutanAsetController::class, 'ajukanPeremajaan'])->name('penyusutan.peremajaan');
    Route::post('/aset-penyusutan/penghapusan/{id}', [PenyusutanAsetController::class, 'ajukanPenghapusan'])->name('penyusutan.penghapusan');
});

    // ⚡ TAMBAHKAN ROUTE INI BRO!
    Route::post('/ditemukan/{id_hilang}', [AsetHilangController::class, 'ditemukan'])->name('hilang.ditemukan');
    
        Route::get('/verify-document/{id}', [AsetHilangController::class, 'verifyPublic'])
            ->name('hilang.verify_public')
            ->middleware('signed');
    });

    // Route monitoring pimpinan yang menumpang ke fungsi index AssetController
    Route::get('/monitoring/assets', [AssetController::class, 'index'])->name('monitoring.assets');
    

    Route::resource('maintenances', MaintenanceController::class);
    
    // Approval Routes
    Route::middleware(['auth', 'role:super admin,kadept,kadiv'])->group(function () {
        Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    });

    // 2. Gerbang Kedua: HANYA Kadept dan Kadiv yang bisa masuk ke Detail (Show) dan Eksekusi (Approve/Reject)
    // Super Admin & Staff OTOMATIS diblokir di sini!
    Route::middleware(['auth', 'role:kadept,kadiv'])->group(function () {
        Route::get('/approvals/{approval}', [ApprovalController::class, 'show'])->name('approvals.show');
        Route::post('/approvals/{id}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('/approvals/{id}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
    });

    Route::get('/berita-acara/{id}', [ApprovalController::class, 'showBeritaAcara'])->name('berita-acara.show');
    Route::get('/berita-acara/{id}/pdf',[ApprovalController::class, 'downloadBeritaAcaraPdf'])->name('berita-acara.pdf');

    Route::name('reports.')->group(function () {
        Route::get('/reports', [AssetController::class, 'reportIndex'])->name('index');
        Route::get('/reports/asset-inventaris', [AssetController::class, 'reportAssetPrint'])->name('asset.print');
        Route::get('/reports/maintenance', [MaintenanceController::class, 'reportMaintenancePrint'])->name('maintenance.print');
        Route::get('/reports/mutasi', [AssetController::class, 'reportMutasiPrint'])->name('mutasi.print');
        Route::get('/reports/penyusutan/print', [PenyusutanAsetController::class, 'printLaporanPenyusutan'])->name('penyusutan.print');
        Route::get('/reports/penghapusan/print', [PenghapusanAsetController::class, 'printLaporanPenghapusan'])->name('penghapusan.print');
    });
});
Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
Route::get('/scan/{kode_aset}', [AssetController::class, 'scanQr'])->name('assets.scan') ->middleware('signed');

// Halaman publik untuk verifikasi tanda tangan digital lewat scan QR
Route::get('/verify-approval/{id}', [ApprovalController::class, 'verifyPublicApproval'])->name('approvals.verify')->middleware('signed');;
Route::get('/verify-report', [AssetController::class, 'verifyReport'])->name('reports.verify')->middleware('signed');

Route::get('/scan-login-bridge', [AssetController::class, 'scanLoginBridge'])->name('assets.scan.bridge');

Route::middleware(['auth', SuperAdminMiddleware::class])->group(function () {
    
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
});

require __DIR__.'/auth.php';
