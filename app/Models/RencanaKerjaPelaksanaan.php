<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RencanaKerjaPelaksanaan extends Model
{
    use HasFactory;

    protected $table = 'rencana_kerja_pelaksanaan';
    protected $primaryKey = 'rkp_id';
    public $incrementing = false;

    protected $fillable = [
        'rkp_id',
        'rk_id',
        'pm_id',
    ];

    public $timestamps = true;
}
