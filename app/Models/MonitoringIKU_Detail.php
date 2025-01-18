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
    ];

    public function monitoringIKU()
    {
        return $this->belongsTo(MonitoringIKU::class, 'mti_id', 'mti_id');
    }

    public function targetIndikator()
    {
        return $this->belongsTo(target_indikator::class, 'ti_id', 'ti_id');
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_id', 'unit_id');
    }

    public function monitoring()
    {
        return $this->hasMany(Monitoring::class, 'rk_id', 'rk_id');
    }

    public function rencanaKerja()
    {
        return $this->belongsTo(RencanaKerja::class, 'rk_id', 'rk_id');
    }
    
}
