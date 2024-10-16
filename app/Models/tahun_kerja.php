<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tahun_kerja extends Model
{
    use HasFactory;
    protected $table = 'tahun_kerja';

    protected $primaryKey = 'th_id';
    public $incrementing = false;

    protected $fillable = [
        'th_id',
        'th_tahun',
        'ren_is_aktif',
        'ren_id',
    ];

}
