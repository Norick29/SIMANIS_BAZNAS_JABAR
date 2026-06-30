<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
        'penyusutan' => \App\Models\PenyusutanAset::class,
        'penghapusan' => \App\Models\PenghapusanAset::class
        // Jika nanti PindahAset mau di-morph juga, tinggal tambah di sini:
        // 'pindah' => \App\Models\PindahAset::class,
    ]);
    }
}
