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
        'mtg_tindak_lanjut',
        'mtg_tindak_lanjut_tanggal',
        'mtg_bukti',
        'rk_id',
    ];

    /**
     * Relasi ke model `TahunKerja`
     */
    public function PeriodeMonitoring()
    {
        return $this->belongsTo(PeriodeMonitoring::class, 'pmo_id', 'pmo_id');
    }

    /**
     * Relasi ke model `PeriodeMonev`
     */
    public function rencanakerja()
    {
        return $this->belongsTo(RencanaKerja::class, 'rk_id', 'rk_id');
    }

    /**
     * Relasi ke model `RencanaKerja`
     */
    

    /**
     * Relasi ke model `UnitKerja`
     */
    
}
