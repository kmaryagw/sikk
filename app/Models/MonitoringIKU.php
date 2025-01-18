<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringIKU extends Model
{
    use HasFactory;

    protected $table = 'monitoring_iku';
    protected $primaryKey = 'mti_id';
    public $incrementing = false;
    protected $fillable = [
        'mti_id',
        'th_id',
        'prodi_id',
        'status',
    ];

    public function targetIndikator()
    {
        return $this->belongsTo(target_indikator::class, 'prodi_id', 'prodi_id');
    }

    // public function prodi()
    // {
    //     return $this->belongsToThrough(program_studi::class, target_indikator::class);
    // }

    // public function tahunKerja()
    // {
    //     return $this->belongsToThrough(tahun_kerja::class, target_indikator::class);
    // }

    public function prodi()
    {
        return $this->belongsTo(program_studi::class, 'prodi_id', 'prodi_id');
    }

    // Relasi ke Tahun Kerja
    public function tahunKerja()
    {
        return $this->belongsTo(tahun_kerja::class, 'th_id', 'th_id');
    }

    public function indikatorKinerja()
    {
        return $this->belongsTo(IndikatorKinerja::class, 'ik_id', 'ik_id');
    }

    public function monitoringikuDetail()
    {
        return $this->hasMany(MonitoringIKU_Detail::class, 'mti_id', 'mti_id');
    }

    public function isFilled()
    {
        return $this->monitoringikuDetail()->exists();
    }

    public function monitorings()
    {
        return $this->hasManyThrough(Monitoring::class, MonitoringIKU_Detail::class, 'mti_id', 'rk_id', 'mti_id', 'rk_id');
    }


    public $timestamps = true;
}
