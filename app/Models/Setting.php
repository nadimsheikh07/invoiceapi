<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'value',
    ];

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->tz(env('APP_TIMEZONE', 'UTC'))->format('d-m-Y h:i A');
    }

    public function getUpdatedAtAttribute($date)
    {
        return Carbon::parse($date)->tz(env('APP_TIMEZONE', 'UTC'))->format('d-m-Y h:i A');
    }
}
