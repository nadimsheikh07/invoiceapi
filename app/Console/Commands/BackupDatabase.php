<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $categories = Category::all();
        $items = Item::all();
        $companies = Company::all();
        $customers = Customer::all();
        $users = User::all();
        $setting = Setting::all();
        Storage::disk('public')->put('backup/setting.json', json_encode($setting));
        Storage::disk('public')->put('backup/users.json', json_encode($users));
        Storage::disk('public')->put('backup/companies.json', json_encode($companies));
        Storage::disk('public')->put('backup/customers.json', json_encode($customers));
        Storage::disk('public')->put('backup/categories.json', json_encode($categories));
        Storage::disk('public')->put('backup/items.json', json_encode($items));
    }
}
