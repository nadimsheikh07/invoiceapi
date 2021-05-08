<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // public function setPasswordAttribute($pass)
    // {
    //     $this->attributes['password'] = Hash::make($pass);
    // }


    public function setRefIdAttribute($value)
    {
        $this->attributes['ref_id'] = $value != 'null' ? $value : 0;
    }

    public function setPlanIdAttribute($value)
    {
        $this->attributes['plan_id'] = $value != 'null' ? $value : 0;
    }

    public function reference()
    {
        return $this->belongsTo(User::class, 'ref_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
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
