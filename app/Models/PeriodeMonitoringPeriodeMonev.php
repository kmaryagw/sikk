<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeMonitoringPeriodeMonev extends Model
{
    use HasFactory;

    protected $table = 'periode_monitoring_periode_monev';
    protected $primaryKey = 'pmpm_id';
    public $incrementing = false;

    protected $fillable = [
        'pmpm_id',
        'pmo_id',
        'pm_id',
    ];

    public $timestamps = true;
}