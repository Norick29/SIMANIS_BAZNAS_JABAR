<?php

use App\Http\Controllers\ProfileController;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AmilController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\DivisiController;

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
});



Route::middleware(['auth', SuperAdminMiddleware::class])->group(function () {
    // Add owner-only routes here
    Route::resource('users', UserController::class);
    
});

require __DIR__.'/auth.php';
