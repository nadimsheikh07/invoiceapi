<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\PermissionGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Route;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (Route::getRoutes() as $route) {
            if ($route->getName()) {
                $name = $route->getName();
                $description = '';

                $detail = explode('.', $name);
                $actionName = $detail[0];
                $action = $detail[1];

                $permissionGroup = $this->createGroup($actionName);

                switch ($action) {
                    case 'index':
                        $description = "Can user access {$actionName} list?";
                        break;
                    case 'show':
                        $description = "Can user access {$actionName} detail?";
                        break;
                    case 'store':
                        $description = "Can user add {$actionName} data?";
                        break;
                    case 'update':
                        $description = "Can user update {$actionName} data?";
                        break;
                    case 'destroy':
                        $description = "Can user delete {$actionName} data?";
                        break;

                    default:
                        $description = "{$actionName} {$action}";
                        break;
                }

                $insertData['permission_group_id'] = $permissionGroup->id;
                $insertData['code'] = $name;
                $insertData['name'] = $description;
                Permission::create($insertData);
            }
        }

        $permissionGroup = $this->createGroup('settings');
        Permission::create([
            'permission_group_id' => $permissionGroup->id,
            'code' => 'settings',
            'name' => "Can user update settings data?"
        ]);
    }


    private function createGroup($name)
    {
        $permissionGroup = PermissionGroup::where('name', $name)->first();
        if (!$permissionGroup) {
            $permissionGroup = PermissionGroup::create(['name' => $name]);
        }
        return $permissionGroup;
    }
}
