<?php

namespace App\Helpers;

use App\Models\Setting;

class SettingHelper
{

    public static function getSetting($code)
    {
        $query = Setting::where('code', $code)->first();
        return $query->value;
    }
}
