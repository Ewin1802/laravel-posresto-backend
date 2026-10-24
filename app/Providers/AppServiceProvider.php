<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
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
        // Tampilan paginasi bertema ARCH untuk seluruh halaman admin.
        Paginator::defaultView('vendor.pagination.arch');

        // Nama hari & bulan dalam Bahasa Indonesia (translatedFormat).
        Carbon::setLocale(config('app.locale', 'id'));
    }
}
