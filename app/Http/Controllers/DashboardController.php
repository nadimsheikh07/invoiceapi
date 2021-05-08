<?php

namespace App\Http\Controllers;

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


        return response()->data($data);
    }
}
