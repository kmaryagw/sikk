<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryMonitoringIKU extends Model
{
    use HasFactory;

    protected $table = 'history_monitoring_iku';

    protected $primaryKey = 'hmi_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'hmi_id',
        'mtid_id',
        'ti_id',
        'hmi_target',
        'hmi_capaian',
        'hmi_keterangan',
        'hmi_status',
        'hmi_url',
        'hmi_evaluasi',
        'hmi_tindaklanjut',
        'hmi_peningkatan',
    ];

    public function monitoringDetail()
    {
        return $this->belongsTo(MonitoringIKU_Detail::class, 'mtid_id', 'mtid_id');
    }
}
