<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/* ====================================================================== */
// Website Route...
/* ====================================================================== */
Route::group(['domain' => 'www.tatcoop.dev',
    'namespace' => 'Website',
    'middleware' => 'locale:th'], function () {

    // Auth Route...
    Route::controller('/auth', 'AuthController', [
        'getLogin' => 'website.auth.login',
        'getRegister' => 'website.auth.register',
        'getLogout' => 'website.auth.logout',
    ]);

    // Password Route...
    Route::controller('/password', 'PasswordController', [
        'getRecovery' => 'website.password.recovery',
    ]);

    // Users Filter...
    Route::group(['middleware' => 'auth:users'], function () {

        // Member Route...
        Route::controller('/member', 'MemberController', [
            'getIndex' => 'website.member.index',
            'getAdmin' => 'website.member.admin',
        ]);
    });

    // Homepage Route...
    Route::controller('/', 'HomepageController', [
        'getIndex' => 'website.index',
        'getAnnounce' => 'website.announce',
    ]);
});

/* ====================================================================== */
// Admin Route...
/* ====================================================================== */
Route::group(['domain' => 'admin.tatcoop.dev',
    'namespace' => 'Admin',
    'middleware' => 'locale:en'], function () {

    // Auth Route...
    Route::controller('/auth', 'AuthController', [
        'getLogin' => 'admin.auth.login',
        'getLogout' => 'admin.auth.logout',
    ]);

    // Administartors Filter...
    Route::group(['middleware' => 'auth:admins'], function () {

        // Admin Route...
        Route::controller('/', 'AdminController', [
            'getIndex' => 'admin.index',
            'getUnauthorize' => 'admin.unauthorize',
        ]);
    });
});
