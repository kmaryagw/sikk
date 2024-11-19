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

    public function tahun_kerja()
    {
        return $this->belongsTo(tahun_kerja::class, 'th_id', 'th_id');
    }

    public function prodi()
    {
        return $this->belongsTo(program_studi::class, 'prodi_id', 'prodi_id');
    }

    // Model Evaluasi
    public function isFilled()
    {
        // Misalnya cek apakah ada field tertentu yang terisi
        return !empty($this->field_name); // Sesuaikan dengan field yang ingin diperiksa
    }


    public $timestamps = true;
}
