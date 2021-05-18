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
            'title' => __('dashboard.total_users'),
            'total' => User::count(),
            'icon' => 'person',
            'url' => 'users'
        ];
        $data[] = [
            'title' => __('dashboard.total_categories'),
            'total' => Category::count(),
            'icon' => 'category',
            'url' => 'categories'
        ];
        $data[] = [
            'title' => __('dashboard.total_items'),
            'total' => Item::count(),
            'icon' => 'donut_small',
            'url' => 'items'
        ];
        $data[] = [
            'title' => __('dashboard.total_purchase'),
            'total' => Purchase::count(),
            'icon' => 'inventory',
            'url' => 'purchases'
        ];
        $data[] = [
            'title' => __('dashboard.total_sales'),
            'total' => Sale::count(),
            'icon' => 'receipt',
            'url' => 'sales'
        ];


        return response()->data($data);
    }
}
