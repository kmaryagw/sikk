<?php

namespace App\Observers;

use App\Models\Monitoring;
use App\Models\PeriodeMonitoring;

class PeriodeMonitoringObserver
{
    /**
     * Handle the PeriodeMonitoring "created" event.
     */
    public function created(PeriodeMonitoring $periodeMonitoring)
    {
        // Ambil semua rencana kerja terkait dengan tahun kerja ini
        $rencanaKerjas = $periodeMonitoring->tahunKerja->rencanaKerjas;

        foreach ($rencanaKerjas as $rencanaKerja) {
            Monitoring::firstOrCreate([
                'mtg_id' => uniqid(),
                'pmo_id' => $periodeMonitoring->pmo_id,
                'rk_id' => $rencanaKerja->rk_id,
            ], [
                'mtg_capaian' => null,
                'mtg_kondisi' => null,
            ]);
        }
    }

    /**
     * Handle the PeriodeMonitoring "updated" event.
     */
    public function updated(PeriodeMonitoring $periodeMonitoring): void
    {
        //
    }

    /**
     * Handle the PeriodeMonitoring "deleted" event.
     */
    public function deleted(PeriodeMonitoring $periodeMonitoring): void
    {
        //
    }

    /**
     * Handle the PeriodeMonitoring "restored" event.
     */
    public function restored(PeriodeMonitoring $periodeMonitoring): void
    {
        //
    }

    /**
     * Handle the PeriodeMonitoring "force deleted" event.
     */
    public function forceDeleted(PeriodeMonitoring $periodeMonitoring): void
    {
        //
    }
}
