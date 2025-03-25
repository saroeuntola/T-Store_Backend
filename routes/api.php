<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\UserController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//public route
Route::post('login',[AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

//GoogleAuth


Route::prefix('products')->group(function(){
    Route::get('/list', [ProductController::class, 'index']);
    Route::get('/show/{id}', [ProductController::class, 'show']);

});
Route::get('banner/list', [BannerController::class, 'index']);
 Route::get('brand/list', [BrandController::class, 'index']);


// protected route
Route::middleware('jwt.auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
    Route::apiResource('/permissions',PermissionController::class);

    Route::apiResource('/category',CategoryController::class);
    Route::prefix('products')->group(function(){
        Route::post('/create', [ProductController::class,'store']);
        Route::post('/update/{id}', [ProductController::class,'update']);
        Route::delete('/delete/{id}', [ProductController::class,'destroy']);
        Route::put('/update_status/{id}', [ProductController::class, 'UpdateStatus']);
    });

    Route::apiResource('/orders',OrdersController::class);
    Route::apiResource('/colors',ColorController::class);
    Route::apiResource('/sizes',SizeController::class);
    Route::post('/update_banner/{id}', [BannerController::class, 'update']);

    Route::prefix('users')->group(function () {
            Route::get('/list', [UserController::class, 'index']);
            Route::post('/create', [UserController::class, 'store']);
            Route::get('/show/{id}', [UserController::class, 'show']);
            Route::put('/update/{id}', [UserController::class, 'update']);
            Route::put('/update_password/{id}', [UserController::class, 'updatePassword']);
            Route::delete('/delete/{id}', [UserController::class, 'destroy']);
            Route::put('/update_status/{id}', [UserController::class, 'UpdateStatus']);
            Route::post('/upload_profile/{id}', [UserController::class, 'addProfilePicture']);

        });
         Route::prefix('brand')->group(function () {
            Route::post('/create', [BrandController::class, 'store']);
            Route::get('/show/{id}', [BrandController::class, 'show']);
            Route::post('/update/{id}', [BrandController::class, 'update']);
            Route::delete('/delete/{id}', [BrandController::class, 'destroy']);
        });

        Route::prefix('banner')->group(function () {
            Route::post('/create', [BannerController::class, 'store']);
            Route::get('/show/{id}', [BannerController::class, 'show']);
            Route::post('/update/{id}', [BannerController::class, 'update']);
            Route::delete('/delete/{id}', [BannerController::class, 'destroy']);
        });
         Route::prefix('roles')->group(function () {
            Route::get('/list', [RolesController::class, 'index']);
            Route::post('/create', [RolesController::class, 'store']);
            Route::get('/show/{id}', [RolesController::class, 'show']);
            Route::get('/edit/{id}', [RolesController::class, 'edit']);
            Route::put('/update/{id}', [RolesController::class, 'update']);
            Route::get('/get_persimssion', [RolesController::class, 'getPermission']);
            Route::delete('/delete/{id}', [RolesController::class, 'destroy']);
        });
});
