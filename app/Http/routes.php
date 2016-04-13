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

Route::group(['middleware' => ['web']], function () {

    /* ====================================================================== */
    // Website Route...
    /* ====================================================================== */
    Route::group(['domain' => 'www.tatcoop.dev'], function () {

        // Website Filter...
        Route::group(['namespace' => 'Website'], function () {

            // Homepage Route...
            Route::controller('/', 'HomepageController');

            // Auth Route...
            Route::controller('/auth', 'AuthController');

            // Member Filter...
            Route::group(['middleware' => 'auth'], function () {

                // Member Route...
                Route::controller('/member', 'MemberController');
            });
        });
    });

    /* ====================================================================== */
    // Admin Route...
    /* ====================================================================== */
    Route::group(['domain' => 'admin.tatcoop.dev'], function () {

        // Admin Filter...
        Route::group(['namespace' => 'Admin'], function() {

            // Auth Route...
            Route::controller('/auth', 'AuthController');

            // Auth Filter...
            Route::group(['middleware' => 'auth'], function () {

                // Admin Route...
                Route::controller('/', 'AdminController');
            });
        });
    });
});
