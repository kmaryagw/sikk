<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class target_indikator extends Model
{
    use HasFactory;

    protected $table = 'target_indikator';

    // Menentukan primary key
    protected $primaryKey = 'ti_id';

    protected $keyType = 'string';

    protected $fillable = [
        'ti_id',
        'ik_id',
        'ti_target',
        'ti_keterangan',
        'prodi_id',
        'th_id'
    ];

    public $timestamps = true;

    public function indikatorKinerja()
    {
        return $this->belongsTo(IndikatorKinerja::class, 'ik_id', 'ik_id');
    }

    public function prodi()
    {
        return $this->belongsTo(program_studi::class, 'prodi_id','prodi_id');
    }

    public function tahunKerja()
    {
        return $this->belongsTo(tahun_kerja::class, 'th_id', 'th_id');
    }

    public function rencanaKerjas()
    {
        return $this->belongsToMany(RencanaKerja::class, 'rencana_kerja_target_indikator', 'ti_id', 'rk_id');
    }

    public function monitoringDetail()
    {
        return $this->hasOne(MonitoringIKU_Detail::class, 'ti_id', 'ti_id');
    }



}