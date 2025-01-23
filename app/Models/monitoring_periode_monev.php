<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class monitoring_periode_monev extends Model
{
    protected $table = 'monitoring_periode_monev';

    protected $fillable = [
        'mtg_id',
        'pm_id',
    ];
}
