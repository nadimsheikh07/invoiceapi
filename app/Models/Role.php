<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    protected $appends = ['permissions', 'permission_codes'];


    public function getPermissionCodesAttribute()
    {
        $permission = $this->belongsToMany(Permission::class);
        return $permission->pluck('code');
    }
    public function getPermissionsAttribute()
    {
        $permission = $this->belongsToMany(Permission::class);
        return $permission->pluck('permissions.id');
    }

    public function rolePermissions()
    {
        return $this->belongsToMany(Permission::class);
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
