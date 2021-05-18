<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jsonString = file_get_contents(base_path('public/storage/backup/categories.json'));
        $data = json_decode($jsonString, true);
        foreach ($data as  $value) {
            $allowed = ['name'];
            $insertData = Arr::only($value, $allowed);
            Category::create($insertData);
        }
    }
}
