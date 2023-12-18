<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\GoodsReceiptController;

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

//Restricted access to authetication prefix
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);    
    Route::get('/users', [AuthController::class, 'users']);    
});

//Restricted access only product-list|product-create|product-edit|product-delete user permissions
Route::group([
    'middleware' => 'api',
    'prefix' => 'product'
], function ($router) {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'show'])->where('id', '[0-9]+');
    Route::get('/all', [ProductController::class, 'all']);
    Route::get('/allbysupplier/{idSupplier}', [ProductController::class, 'allBySupplier'])->where('idSupplier', '[0-9]+');
    Route::post('/create', [ProductController::class, 'store']);
    Route::post('/delete', [ProductController::class, 'destroy']);
    Route::post('/update', [ProductController::class, 'update']);    
});

//Restricted access only config-list|config-create|config-edit|config-delete user and general configs
Route::group([
    'middleware' => 'api',
    'prefix' => 'config'
], function ($router) {
    Route::get('/', [ConfigController::class, 'indexAll']);
    Route::get('/{id}', [ConfigController::class, 'show'])->where('id', '[0-9]+');
    Route::post('/update', [ConfigController::class, 'update']);
    Route::post('/user/update/{id}', [ConfigController::class, 'updateUserConfig'])->where('id', '[0-9]+');
    Route::post('/user/delete/{id}', [ConfigController::class, 'deleteUserConfig'])->where('id', '[0-9]+');
    Route::get('/user/{id}', [ConfigController::class, 'users'])->where('id', '[0-9]+');
    Route::get('/{id}/users', [ConfigController::class, 'configs'])->where('id', '[0-9]+');    
});

//Restricted access only role-list|role-create|role-edit|role-delete user roles
Route::group([
    'middleware' => 'api',
    'prefix' => 'role'
], function ($router) {
    Route::get('/', [RoleController::class, 'index']);
    Route::get('/{id}', [RoleController::class, 'show'])->where('id', '[0-9]+');
    Route::get('/user/{id}', [RoleController::class, 'userRole'])->where('id', '[0-9]+');
    Route::post('/create', [RoleController::class, 'create']);
    Route::post('/update/{id}', [RoleController::class, 'update'])->where('id', '[0-9]+');
    Route::post('/delete/{id}', [RoleController::class, 'delete'])->where('id', '[0-9]+');
    Route::post('/user/{id}', [RoleController::class, 'userAssignRole'])->where('id', '[0-9]+');
});

//Restricted access only permission-list|permission-create|permission-edit|permission-delete user permissions
Route::group([
    'middleware' => 'api',
    'prefix' => 'permission'
], function ($router) {
    Route::get('/', [PermissionController::class, 'index']);
    Route::get('/{id}', [PermissionController::class, 'show'])->where('id', '[0-9]+');
    Route::get('/user/{id}', [PermissionController::class, 'userPermissions'])->where('id', '[0-9]+');
    Route::post('/user/{id}', [PermissionController::class, 'userAssignRole'])->where('id', '[0-9]+');
});

//Restricted access only supplier-list|supplier-create|supplier-edit|supplier-delete user suppliers
Route::group([
    'middleware' => 'api',
    'prefix' => 'supplier'
], function ($router) {
    Route::get('/', [SupplierController::class, 'index']);
    Route::get('/all', [SupplierController::class, 'all']);
    Route::get('/{id}', [SupplierController::class, 'show'])->where('id', '[0-9]+');
    Route::post('/create', [SupplierController::class, 'create']);
    Route::post('/update/{id}', [SupplierController::class, 'update'])->where('id', '[0-9]+');
    Route::post('/delete/{id}', [SupplierController::class, 'delete'])->where('id', '[0-9]+');
});

//Restricted access only goodsreceipt-list|goodsreceipt-create|goodsreceipt-edit|goodsreceipt-delete user goodsreceipts
Route::group([
    'middleware' => 'api',
    'prefix' => 'goodsreceipt'
], function ($router) {
    Route::get('/', [GoodsReceiptController::class, 'index']);
    Route::get('/all', [GoodsReceiptController::class, 'all']);
    Route::get('/allbysupplier/{idSupplier}', [GoodsReceiptController::class, 'allBySupplier'])->where('idSupplier', '[0-9]+');
    Route::get('/{id}', [GoodsReceiptController::class, 'show'])->where('id', '[0-9]+');
    Route::get('/{id}/products', [GoodsReceiptController::class, 'products'])->where('id', '[0-9]+');
    Route::post('/getPriceEst', [GoodsReceiptController::class, 'getPriceEst']);
    Route::post('/{id}/product/add', [GoodsReceiptController::class, 'addProduct'])->where('id', '[0-9]+');
    Route::post('/{id}/product/delete', [GoodsReceiptController::class, 'deleteProduct'])->where('id', '[0-9]+');
    Route::post('/create', [GoodsReceiptController::class, 'create']);
    Route::post('/update/{id}', [GoodsReceiptController::class, 'update'])->where('id', '[0-9]+');
    Route::post('/delete/{id}', [GoodsReceiptController::class, 'delete'])->where('id', '[0-9]+');
});

//Catch error when fail on endpoint
Route::fallback(function(){
    return response()->json(['error' => 'Página no encontrada'], 404);
});