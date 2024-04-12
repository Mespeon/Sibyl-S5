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
        Route::post('forgot-password', ['uses' => 'AuthorizationController@forgotPassword']);
        Route::post('reset-password', ['uses' => 'AuthorizationController@resetPassword']);
    });

    Route::get('selections', ['uses' => 'SelectionsController@getSelection']);
    
    Route::prefix('user')->group(function () {
        Route::prefix('profile')->group(function () {
            Route::get('/', ['uses' => 'UserController@getProfile']);
            Route::post('update', ['uses' => 'UserController@updateProfile']);

            // TO-DO: Hide behind a permission middleware.
            // Route::post('student/update', ['uses' => 'UserController@updateStudentProfile']);
            // Route::post('faculty/profile', ['uses' => 'UserController@updateFacultyProfile']);
        });

        Route::prefix('account')->group(function () {
            Route::post('change-password', ['uses' => 'UserController@changePassword']);
        });
    });

    Route::prefix('admin')->middleware('admin.access')->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('/', ['uses' => 'AdministratorController@getUsers']);
            Route::post('create', ['uses' => 'AdministratorController@createUser']);
            Route::post('update', ['uses' => 'AdministratorController@updateUser']);
            Route::put('deactivate', ['uses' => 'AdministratorController@updateUserAccountStatus']);
            Route::delete('remove', ['uses' => 'AdministratorController@removeUserAccount']);
            Route::post('reset-password', ['uses' => 'AdministratorController@resetUserPassword']);
        });

        Route::prefix('roles')->group(function () {
            Route::get('/', ['uses' => 'AdministratorController@retrieveRoles']);
            // Route::post('/add', []);
            // Route::put('/update', []);
            // Route::delete('/remove', []);
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

    Route::prefix('mailables')->group(function () {
        Route::get('forgotpassword', function () {
            return new App\Mail\ForgotPassword('123abc');
        });
    });

    Route::get('test-auth', ['uses' => 'AuthorizationController@test']);
});
