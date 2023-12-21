<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resttime extends Model
{
    use HasFactory;

    protected $fillable = [
        'worktime_id',
        'work_day',
        'rest_start_at',
        'rest_end_at',
        'rest_time'
    ];

    public function worktime()
    {
        return $this->belongsTo(Worktime::class);
    }

}
