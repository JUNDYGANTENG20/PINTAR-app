<?php

namespace App\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        Carbon::setLocale('id');

        // Jumlah pengingat aktif untuk ikon lonceng di header (FR-05).
        View::composer('layouts.app', function ($view) {
            $user = auth()->user();
            $view->with('jumlahPengingat', $user
                ? $user->kegiatanDiingatkan()->whereDate('tgl_tutup', '>=', today())->count()
                : 0);
        });
    }
}
