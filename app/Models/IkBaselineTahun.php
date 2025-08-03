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
        'th_tahun',
        'baseline',
    ];

    public function indikatorKinerja()
    {
        return $this->belongsTo(IndikatorKinerja::class, 'ik_id', 'ik_id');
    }

    public function tahunKerja()
    {
        return $this->belongsTo(tahun_kerja::class, 'th_id', 'th_id');
    }
}
