<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jsonString = file_get_contents(base_path('public/storage/backup/customers.json'));
        $data = json_decode($jsonString, true);
        foreach ($data as  $value) {
            $allowed = ['name', 'email', 'contact', 'gstin', 'address'];
            $insertData = Arr::only($value, $allowed);
            Customer::create($insertData);
        }
    }
}
