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
    ];

    public function tahun_kerja()
    {
        return $this->belongsTo(tahun_kerja::class, 'th_id', 'th_id');
    }

    public function prodi()
    {
        return $this->belongsTo(program_studi::class, 'prodi_id', 'prodi_id');
    }

    public $timestamps = true;
}
