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

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/home', function () {
    return view('home');
})->middleware(['auth', 'verified'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/amil', [AmilController::class, 'index'])->name('amil.index');
    Route::post('/amil', [AmilController::class, 'store'])->name('amil.store');
    Route::get('/jabatan', [JabatanController::class, 'index'])->name('jabatan.index');
    Route::post('/jabatan', [JabatanController::class, 'store'])->name('jabatan.store');
    Route::get('/divisi', [DivisiController::class, 'index'])->name('divisi.index');
    Route::post('/divisi', [DivisiController::class, 'store'])->name('divisi.store');
    Route::post('/master/amil/import', [AmilController::class, 'import'])
    ->name('amil.import');
    Route::put('/amil/{id}', [AmilController::class, 'update'])->name('amil.update');
    Route::delete('/amil/{id}', [AmilController::class, 'destroy'])->name('amil.destroy');
    Route::put('/jabatan/{id}', [JabatanController::class, 'update'])
    ->name('jabatan.update');
    Route::delete('/jabatan/{id}', [JabatanController::class, 'destroy'])
    ->name('jabatan.destroy');
    Route::put('/divisi/{id}', [DivisiController::class, 'update'])
    ->name('divisi.update');
    Route::delete('/divisi/{id}', [DivisiController::class, 'destroy'])
    ->name('divisi.destroy');
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::post('/kategori/store', [KategoriController::class, 'store'])->name('kategori.store');
    Route::put('/kategori/{id}', [KategoriController::class, 'update'])
    ->name('kategori.update');
    Route::delete('/kategori/{id}', [KategoriController::class, 'destroy'])
    ->name('kategori.destroy');
    Route::get('/lokasi', [LokasiController::class, 'index'])->name('lokasi.index');
    Route::post('/lokasi/store', [LokasiController::class, 'store'])->name('lokasi.store');
    Route::put('/lokasi/{id}', [LokasiController::class, 'update'])
    ->name('lokasi.update');
    Route::delete('/lokasi/{id}', [LokasiController::class, 'destroy'])
    ->name('lokasi.destroy');
    Route::post('/master/lokasi/import', [LokasiController::class, 'import'])
    ->name('lokasi.import');
    Route::get('/barang', [BarangController::class, 'index'])->name('barang.index');
    Route::post('/barang/store', [BarangController::class, 'store'])->name('barang.store');
    Route::put('/barang/{id}', [BarangController::class, 'update'])
    ->name('barang.update');
    Route::delete('/barang/{id}', [BarangController::class, 'destroy'])
    ->name('barang.destroy');
    Route::post('/master/barang/import', [BarangController::class, 'import'])
    ->name('barang.import');
    Route::get('/jenis', [JenisController::class, 'index'])->name('jenis.index');
    Route::post('/jenis/store', [JenisController::class, 'store'])->name('jenis.store');
    Route::put('/jenis/{id}', [JenisController::class, 'update'])
    ->name('jenis.update');
    Route::delete('/jenis/{id}', [JenisController::class, 'destroy'])
    ->name('jenis.destroy');
    Route::post('/master/jenis/import', [JenisController::class, 'import'])
    ->name('jenis.import');
    Route::get('/sumber', [SumberController::class, 'index'])->name('sumber.index');
    Route::post('/sumber/store', [SumberController::class, 'store'])->name('sumber.store');
    Route::put('/sumber/{id}', [SumberController::class, 'update'])
    ->name('sumber.update');
    Route::delete('/sumber/{id}', [SumberController::class, 'destroy'])
    ->name('sumber.destroy');
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::post('/assets/store', [AssetController::class, 'store'])->name('assets.store');
});



Route::middleware(['auth', SuperAdminMiddleware::class])->group(function () {
    // Add owner-only routes here
    Route::resource('users', UserController::class);
    
});

require __DIR__.'/auth.php';
