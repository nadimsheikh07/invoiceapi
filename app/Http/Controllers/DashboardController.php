<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [];

        $data[] = [
            'title' => 'Total Users',
            'total' => User::count(),
            'icon' => 'person',
            'url' => 'users'
        ];
        $data[] = [
            'title' => 'Total Categories',
            'total' => Category::count(),
            'icon' => 'category',
            'url' => 'categories'
        ];
        $data[] = [
            'title' => 'Total Items',
            'total' => Item::count(),
            'icon' => 'donut_small',
            'url' => 'items'
        ];
        $data[] = [
            'title' => 'Total Purchase',
            'total' => Purchase::count(),
            'icon' => 'inventory',
            'url' => 'purchases'
        ];
        $data[] = [
            'title' => 'Total Sales',
            'total' => Sale::count(),
            'icon' => 'receipt',
            'url' => 'sales'
        ];


        return response()->data($data);
    }
}
