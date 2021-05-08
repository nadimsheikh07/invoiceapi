<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jsonString = file_get_contents(base_path('resources/json/setting.json'));
        $data = json_decode($jsonString, true);
        foreach ($data as  $value) {
            $allowed = ['code', 'value'];
            $insertData = Arr::only($value, $allowed);
            Setting::create($insertData);
        }
    }
}
