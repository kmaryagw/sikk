<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringIKU_Detail extends Model
{
    use HasFactory;

    protected $table = 'monitoring_iku_detail';

    protected $primaryKey = 'mtid_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'mtid_id',
        'mti_id',
        'ti_id',
        'mtid_target',
        'mtid_capaian',
        'mtid_keterangan',
        'mtid_status',
        'mtid_url',
        'mtid_evaluasi',
        'mtid_tindaklanjut',
        'mtid_peningkatan',
    ];

    public function monitoringIKU()
    {
        return $this->belongsTo(MonitoringIKU::class, 'mti_id', 'mti_id');
    }

    public function targetIndikator()
    {
        return $this->belongsTo(target_indikator::class, 'ti_id', 'ti_id');
    }

    // Mengubah relasi ke 'UnitKerja' dengan relasi many-to-many melalui 'indikatorkinerja_unitkerja'
    public function unitKerja()
    {
        return $this->belongsToMany(UnitKerja::class, 'indikatorkinerja_unitkerja', 'ik_id', 'unit_id')
                    ->withPivot('ik_id', 'unit_id');
    }

    public function monitoring()
    {
        return $this->hasMany(Monitoring::class, 'rk_id', 'rk_id');
    }

    public function rencanaKerja()
    {
        return $this->belongsTo(RencanaKerja::class, 'rk_id', 'rk_id');
    }

    // Mengubah relasi ke 'IndikatorKinerja' melalui relasi yang benar
    public function indikatorKinerja()
    {
        return $this->belongsTo(IndikatorKinerja::class, 'ti_id', 'ik_id');
    }
}
