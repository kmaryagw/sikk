<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringFinalUnit extends Model
{
    use HasFactory;

    protected $table = 'monitoring_final_units';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'monitoring_iku_id',
        'unit_id',
        'status',
        'finalized_by',
        'finalized_at',
    ];

    // Relasi opsional (buat kalau kamu mau akses langsung)
    public function monitoringIKU()
    {
        return $this->belongsTo(MonitoringIKU::class, 'monitoring_iku_id', 'mti_id');
    }

    public function unitkerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_id', 'unit_id');
    }

    public function finalizedBy()
    {
        return $this->belongsTo(User::class, 'finalized_by', 'id');
    }
}
