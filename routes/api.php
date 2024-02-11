<?php

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

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('guest', ['uses' => 'AuthorizationController@generateGuestAccessToken']);
        Route::post('register', ['uses' => 'AuthorizationController@register']);
        Route::post('login', ['uses' => 'AuthorizationController@login']);
        Route::post('logout', ['uses' => 'AuthorizationController@logout']);
    });

    Route::get('selections', ['uses' => 'SelectionsController@getSelection']);
    
    Route::prefix('user')->group(function () {

    });

    Route::prefix('admin')->middleware('admin.access')->group(function () {
        Route::prefix('roles')->group(function () {
            Route::get('/', ['uses' => 'AdministratorController@retrieveRoles']);
            Route::post('/add', []);
            Route::put('/update', []);
            Route::delete('/remove', []);
        });

        Route::prefix('permissions')->group(function () {

        });
    });
});

Route::prefix('mwpsb')->group(function () {
    Route::get('about', function () {
        $resparams = [
            'codename' => env('APP_NAME'),
            'laravel_version' => app()->version(),
            'api_version' => env('APP_VERSION'),
            'api_sha1_fingerprint' => sha1(env('APP_VERSION'))
        ];
        return $resparams;
    });

    Route::get('test-auth', ['uses' => 'AuthorizationController@test']);
});
