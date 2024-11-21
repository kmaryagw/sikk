<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluasi extends Model
{
    use HasFactory;

    protected $table = 'evaluasi';
    protected $primaryKey = 'eval_id';
    public $incrementing = false;
    protected $fillable = [
        'eval_id',
        'th_id',
        'prodi_id',
        'status',
    ];

    public function targetIndikator()
    {
        return $this->belongsTo(target_indikator::class, 'th_id', 'th_id');
    }

    public function prodi()
    {
        return $this->belongsToThrough(program_studi::class, target_indikator::class);
    }

    public function tahunKerja()
    {
        return $this->belongsToThrough(tahun_kerja::class, target_indikator::class);
    }

    public function indikatorKinerja()
    {
        return $this->belongsToThrough(IndikatorKinerjaUtama::class, target_indikator::class);
    }

    public function evaluasiDetails()
    {
        return $this->hasMany(Evaluasi_Detail::class, 'eval_id', 'eval_id');
    }



    // Model Evaluasi
    public function isFilled()
    {
        // Misalnya cek apakah ada field tertentu yang terisi
        return !empty($this->field_name);
    }


    public $timestamps = true;
}
