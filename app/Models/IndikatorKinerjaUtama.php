<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndikatorKinerjaUtama extends Model
{
    use HasFactory;

    protected $table = 'indikator_kinerja_utama';
    protected $primaryKey = 'ik_id';
    public $incrementing = false;
   protected $fillable = [
        'ik_id',
        'ik_nama',
        'std_id',
    ];
    public function standar()
    {
        return $this->belongsTo(standar::class, 'std_id', 'std_id');
    }
    public $timestamps = true;
}