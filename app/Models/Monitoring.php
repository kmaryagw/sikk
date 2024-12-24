<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitoring extends Model
{
    use HasFactory;

    protected $table = 'monitoring';
    protected $primaryKey = 'mtg_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'mtg_id',
        'pmo_id',
        'mtg_capaian',
        'mtg_kondisi',
        'mtg_kendala',
        'mtg_status',
        'mtg_tindak_lanjut',
        'mtg_tindak_lanjut_tanggal',
        'mtg_bukti',
        'rk_id',
    ];

    public function periodeMonitoring()
    {
        return $this->belongsTo(PeriodeMonitoring::class, 'pmo_id', 'pmo_id');
    }

    public function realisasi()
    {
        return $this->belongsTo(RealisasiRenja::class, 'rkr_id', 'rkr_id');
    }

    public function rencanaKerja()
    {
        return $this->belongsTo(RencanaKerja::class, 'rk_id', 'rk_id');
    }

    public function targetIndikator()
    {
        return $this->belongsTo(target_indikator::class, 'rk_id', 'rk_id');
    }
    
    protected $attributes = [
        'mtg_capaian' => 0,
    ];
}
