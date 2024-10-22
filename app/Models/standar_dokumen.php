<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class standar_dokumen extends Model
{
    use HasFactory;

    protected $table = 'standar_dokumen';
    protected $primaryKey = 'stdd_id';

    protected $fillable = [
        'std_id',
        'stdd_file',
    ];

    public function standar()
    {
        return $this->belongsTo(standar::class, 'std_id', 'std_id');
    }

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($model) {
    //         $model->stdd_id = (string) Str::uuid(); // Generate UUID string
    //     });
    // }
}