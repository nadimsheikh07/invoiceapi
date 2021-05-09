<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jsonString = file_get_contents(base_path('resources/json/items.json'));
        $data = json_decode($jsonString, true);
        foreach ($data as  $value) {
            $allowed = ['category_id','name', 'price', 'track_inventory'];
            $insertData = Arr::only($value, $allowed);
            Item::create($insertData);
        }
    }
}
