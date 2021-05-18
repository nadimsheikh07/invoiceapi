<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jsonString = file_get_contents(base_path('public/storage/backup/companies.json'));
        $data = json_decode($jsonString, true);
        foreach ($data as  $value) {
            $allowed = ['name', 'email', 'contact', 'pancard', 'gstin', 'address', 'bank_name', 'account_number', 'account_type', 'swift_code', 'ifsc_code', 'bank_address'];
            $insertData = Arr::only($value, $allowed);
            Company::create($insertData);
        }
    }
}
