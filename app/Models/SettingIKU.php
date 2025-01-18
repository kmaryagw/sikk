<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingIKU extends Model
{
    use HasFactory;

    protected $table = 'settingiku';
    protected $primaryKey = 'id_setting';
    public $incrementing = false;
    protected $fillable = [
        'id_setting',
        'th_id',
        'ik_id',
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

    public $timestamps = true;
}