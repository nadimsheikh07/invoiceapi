<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'comments',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->tz(env('APP_TIMEZONE', 'UTC'))->format('d-m-Y h:i A');
    }

    public function getUpdatedAtAttribute($date)
    {
        return Carbon::parse($date)->tz(env('APP_TIMEZONE', 'UTC'))->format('d-m-Y h:i A');
    }
}
