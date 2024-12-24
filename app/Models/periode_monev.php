<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class periode_monev extends Model
{
    use HasFactory;

    protected $table = 'periode_monev';
    protected $primaryKey = 'pm_id';
    public $incrementing = false;
   protected $fillable = [
        'pm_id',
        'pm_nama',
    ];

    public function rencanaKerjas()
    {
        return $this->belongsToMany(RencanaKerja::class, 'rencana_kerja_pelaksanaan', 'pm_id', 'rk_id');
    }

    public function periodeMonitorings()
{
    return $this->belongsToMany(PeriodeMonitoring::class, 'periode_monitoring_periode_monev', 'pm_id', 'pmo_id');
}


    public $timestamps = true;

}
