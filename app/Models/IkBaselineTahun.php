<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IkBaselineTahun extends Model
{
    use HasFactory;

    protected $table = 'ik_baseline_tahun';

    protected $fillable = [
        'ik_id',
        'th_id',
        'prodi_id', // tambahan supaya bisa simpan baseline per prodi
        'baseline',
    ];

    /**
     * Relasi ke indikator kinerja
     */
    public function indikatorKinerja()
    {
        return $this->belongsTo(IndikatorKinerja::class, 'ik_id', 'ik_id');
    }

    /**
     * Relasi ke tahun kerja
     */
    public function tahunKerja()
    {
        return $this->belongsTo(tahun_kerja::class, 'th_id', 'th_id');
    }

    /**
     * Relasi ke program studi
     */
    public function programStudi()
    {
        return $this->belongsTo(program_studi::class, 'prodi_id', 'id');
    }
}
