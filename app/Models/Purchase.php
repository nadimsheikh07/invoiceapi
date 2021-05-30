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
        'company_id',
        'total_tax',
        'total_discount',
        'total',
        'comments',
        'is_lock'
    ];

    protected $appends = ['company_name','customer_name'];

    public function getCompanyNameAttribute()
    {
        $name = '';
        if ($this->company) {
            $name = $this->company->name;
        }
        return $name;
    }
    public function getCustomerNameAttribute()
    {
        $name = '';
        if ($this->customer) {
            $name = $this->customer->name;
        }
        return $name;
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

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
