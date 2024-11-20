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

    // Jika masih membutuhkan relasi ke Prodi melalui TargetIndikator
    public function prodi()
    {
        return $this->belongsToThrough(program_studi::class, target_indikator::class);
    }

    // Relasi ke TahunKerja melalui TargetIndikator
    public function tahunKerja()
    {
        return $this->belongsToThrough(tahun_kerja::class, target_indikator::class);
    }

    // Model Evaluasi
    public function isFilled()
    {
        // Misalnya cek apakah ada field tertentu yang terisi
        return !empty($this->field_name);
    }


    public $timestamps = true;
}
