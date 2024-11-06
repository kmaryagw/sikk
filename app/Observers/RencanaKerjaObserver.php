<?php

namespace App\Observers;

use App\Models\Monitoring;
use App\Models\RencanaKerja;

class RencanaKerjaObserver
{
    /**
     * Handle the RencanaKerja "created" event.
     */
    public function created(RencanaKerja $rencanaKerja): void
    {
        {
            // Ambil semua periode monitoring terkait dengan tahun kerja ini
            $periodeMonitorings = $rencanaKerja->tahunKerja->periodeMonitorings;
    
            foreach ($periodeMonitorings as $periode) {
                Monitoring::firstOrCreate([
                    'mtg_id' => uniqid(),
                    'pmo_id' => $periode->pmo_id,
                    'rk_id' => $rencanaKerja->rk_id,
                ], [
                    'mtg_capaian' => null,
                    'mtg_kondisi' => null,
                ]);
            }
        }
    }

    /**
     * Handle the RencanaKerja "updated" event.
     */
    public function updated(RencanaKerja $rencanaKerja): void
    {
        //
    }

    /**
     * Handle the RencanaKerja "deleted" event.
     */
    public function deleted(RencanaKerja $rencanaKerja): void
    {
        //
    }

    /**
     * Handle the RencanaKerja "restored" event.
     */
    public function restored(RencanaKerja $rencanaKerja): void
    {
        //
    }

    /**
     * Handle the RencanaKerja "force deleted" event.
     */
    public function forceDeleted(RencanaKerja $rencanaKerja): void
    {
        //
    }
}
