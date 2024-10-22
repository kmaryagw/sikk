<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndikatorKinerjaUtama extends Model
{
    use HasFactory;

    protected $table = 'indikator_kinerja';
    protected $primaryKey = 'ik_id';
    public $incrementing = false;
   protected $fillable = [
        'ik_id',
        'ik_nama',
        'std_id',
        'th_id',
    ];
    public function standar()
    {
        return $this->belongsTo(standar::class, 'std_id', 'std_id');
    }
    public function tahun_kerja()
    {
        return $this->belongsTo(standar::class, 'th_id', 'th_id');
    }
    public $timestamps = true;
}
