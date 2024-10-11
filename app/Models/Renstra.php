<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Renstra extends Model
{
    use HasFactory;
      protected $table = 'renstra';
      protected $primaryKey = 'ren_id';
      public $incrementing = false;
      protected $keyType = 'string';
      protected $fillable = [
          'ren_id',
          'ren_nama',
          'ren_pimpinan',
          'ren_periode_awal',
          'ren_periode_akhir',
          'ren_is_aktif',
          
      ];
    
}
