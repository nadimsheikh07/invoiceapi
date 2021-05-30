<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PermissionGroupController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('signin', [UserController::class, 'signin']);
    Route::post('signup', [UserController::class, 'signup']);
    Route::post('forgot_password', [UserController::class, 'forgotPassword']);
    Route::group([
        'middleware' => 'auth:sanctum'
    ], function () {
        Route::put('update_password/{id}', [UserController::class, 'updatePassword']);
        Route::get('signout', [UserController::class, 'signout']);
        Route::get('profile', [UserController::class, 'profile']);
        Route::post('check_permission', [UserController::class, 'checkPermission']);
    });
});

Route::group([
    'prefix' => 'export'
], function () {
    Route::get('items', [ItemController::class, 'export']);
});

Route::group([
    'prefix' => 'import'
], function () {
    Route::post('items', [ItemController::class, 'import']);
});

Route::group([
    'prefix' => 'upload'
], function () {
    Route::post('image', [FileController::class, 'image']);
});

Route::group([
    'prefix' => 'pdf'
], function () {
    Route::get('sales/{sale}', [SaleController::class, 'showPdf']);
});

Route::get('download', [FileController::class, 'download']);

Route::group([
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('permission_groups', PermissionGroupController::class);
    Route::apiResource('permissions', PermissionController::class);

    Route::get('settings/update', [SettingController::class, 'index']);
    Route::put('settings/update', [SettingController::class, 'update']);

    Route::get('inventories/update_inventory', [InventoryController::class, 'updateInventory']);

    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('companies', CompanyController::class);
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('inventories', InventoryController::class);
    Route::apiResource('items', ItemController::class);
    Route::apiResource('purchases', PurchaseController::class);
    Route::apiResource('sales', SaleController::class);
});
