<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    protected $fillable = [
        'problem_type',
        'description',
        'user_id',
        'contact',
        'gps_lat',
        'gps_lng',
        'audio_path',
        'video_path',
        'address',
        'status',
    ];

public function user()
{
    return $this->belongsTo(\App\Models\User::class);
}
}