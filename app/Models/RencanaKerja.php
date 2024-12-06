<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RencanaKerja extends Model
{
    use HasFactory;

    protected $table = 'rencana_kerja';
    protected $primaryKey = 'rk_id';
    
    protected $fillable = [
        'rk_id',
        'rk_nama',
        'th_id',
        'unit_id',
    ];

    
    public function indikatorKinerja()
{
    return $this->belongsToMany(IndikatorKinerja::class, 'rencana_kerja_target_indikator', 'rk_id', 'ik_id');
}
    public function tahunKerja()
    {
        return $this->belongsTo(tahun_kerja::class, 'th_id', 'th_id');
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_id', 'unit_id');
    }

    public function periodes()
    {
        return $this->belongsToMany(periode_monev::class, 'rencana_kerja_pelaksanaan', 'rk_id', 'pm_id')->orderBy('pm_nama');
    }

    public function targetindikators()
    {
        return $this->belongsToMany(target_indikator::class, 'rencana_kerja_target_indikator', 'rk_id', 'ti_id')->with('indikatorKinerja');
    }

    public function periodeMonitoring()
    {
        return $this->hasOne(PeriodeMonitoring::class, 'rk_id', 'rk_id');
    }

    public $timestamps = true;

    public function monitoring()
    {
        return $this->hasMany(Monitoring::class, 'rk_id', 'rk_id');
    }
}