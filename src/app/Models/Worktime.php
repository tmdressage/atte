<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worktime extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_day',
        'work_start_at',
        'work_end_at',
        'work_time',
        'total_work_time',
        'total_rest_time'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

        public function resttime()
    {
        return $this->hasMany(Resttime::class);
    }
}
