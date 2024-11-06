<?php

namespace App\Providers;

use App\Models\PeriodeMonitoring;
use App\Models\RencanaKerja;
use App\Observers\PeriodeMonitoringObserver;
use App\Observers\RencanaKerjaObserver;
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
        // RencanaKerja::observe(RencanaKerjaObserver::class);
        // PeriodeMonitoring::observe(PeriodeMonitoringObserver::class);
    }
}
