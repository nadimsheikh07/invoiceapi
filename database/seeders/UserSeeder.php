<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = Permission::get()->pluck('id');

        $role = Role::create(['name' => 'Admin']);

        $role->rolePermissions()->detach();
        $role->rolePermissions()->attach($permissions);

        $data = [
            'role_id' => 1,
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('Abcd@1234'),
            'status' => 1
        ];
        User::create($data);
    }
}
