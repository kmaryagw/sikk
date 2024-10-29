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
        'pm_id',
        'th_id',
        'unit_id',
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
    public function tahunKerja()
    {
        return $this->belongsTo(tahun_kerja::class, 'th_id', 'th_id');
    }

    /**
     * Relasi ke model `PeriodeMonev`
     */
    public function periodeMonev()
    {
        return $this->belongsTo(periode_monev::class, 'pm_id', 'pm_id');
    }

    /**
     * Relasi ke model `RencanaKerja`
     */
    public function rencanaKerja()
    {
        return $this->belongsTo(RencanaKerja::class, 'rk_id', 'rk_id');
    }

    /**
     * Relasi ke model `UnitKerja`
     */
    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'unit_id', 'unit_id');
    }
}
