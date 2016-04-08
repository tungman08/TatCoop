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

    /* ============================================================================= */
    // Website Route...
    /* ============================================================================= */
    Route::group(['domain' => 'www.tatcoop.dev'], function () {
        Route::group(['namespace' => 'Website'], function () {

            // Homepage Route...
            Route::get('/', [
                'as' => 'homepage',
                'uses' => 'HomeController@showIndex'
            ]);

            // PR Route...
            Route::group(['prefix' => 'pr'], function () {

                // Matches The "/pr/docs" URL
                Route::get('docs', [
                    'as' => 'docs',
                    'uses' => 'PrController@showDocs'
                ]);
            });

            // Auth Route...
            Route::group(['middleware' => 'auth'], function () {

            });
        });
    });
    
    /* ============================================================================= */
    // Admin Route...
    /* ============================================================================= */
    Route::group(['domain' => 'admin.tatcoop.dev'], function () {
        Route::group(['namespace' => 'Admin'], function() {

            // Admin page Route...
            Route::get('/', [
                'as' => 'adminpage',
                'uses' => 'HomeController@showIndex'
            ]);
        });
    });
});
