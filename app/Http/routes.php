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

    // Verify Route...
    Route::get('/auth/verify/{token}', ['as' => 'website.auth.verify', 'uses' => 'AuthController@getVerify']);

    // Auth Route...
    Route::controller('/auth', 'AuthController', [
        'getRegister' => 'website.auth.register',
        'getLogin' => 'website.auth.login',
        'getLogout' => 'website.auth.logout',
    ]);

    // Password Route...
    Route::group(['prefix' => '/password'], function() {
        Route::get('email', ['as' => 'website.password.email', 'uses' => 'PasswordController@getEmail']);
        Route::get('reset/{token}', ['as' => 'website.password.reset', 'uses' => 'PasswordController@getReset']);
        Route::post('email', 'PasswordController@postEmail');
        Route::post('reset', 'PasswordController@postReset');
    });

    // Member Route...
    Route::controller('/member', 'MemberController', [
        'getIndex' => 'website.member.index',
        'getEdit' => 'website.member.edit',
        'putUpdate' => 'website.member.update',
        'getShareholding' => 'website.member.shareholding',
        'getLoan' => 'website.member.loan',
        'getDividend' => 'website.member.dividend',
        'getGuaruntee' => 'website.member.guaruntee',
    ]);

    // Billing Route...
    Route::group(['prefix' => '/member/shareholding/billing'], function() {
        Route::get('{date}', ['as' => 'website.member.shareholding.billing', 'uses' => 'MemberController@getBilling']);
        Route::get('{date}/print', ['as' => 'website.member.shareholding.billing.print', 'uses' => 'MemberController@getPrintBilling']);
        Route::get('{date}/pdf', ['as' => 'website.member.shareholding.billing.pdf', 'uses' => 'MemberController@getPdfBilling']);
    });

    // Profile Route...
    Route::controller('/user', 'UserController', [
        'getProfile' => 'user.profile',
        'getPassword' => 'user.password',
        'getAlert' => 'user.alert',
        'getNotice' => 'user.notice',
        'getMessage' => 'user.message',
    ]);

    // Ajax Route...
    Route::controller('/ajax', 'AjaxController', [
        'getBackground' => 'website.ajax.background',
    ]);

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
    'middleware' => 'locale:th'], function () {

    // Auth Route...
    Route::controller('/auth', 'AuthController', [
        'getLogin' => 'admin.auth.login',
        'getLogout' => 'admin.auth.logout',
    ]);

    // Ajax Route...
    Route::controller('/ajax', 'AjaxController', [
        'getBackground' => 'admin.ajax.background',
        'getMembers' => 'admin.ajax.members',
        'getDistricts' => 'admin.ajax.district',
        'getSubdistricts' => 'admin.ajax.subdistrict',
        'getPostcode' => 'admin.ajax.postcode',
        'getStatus' => 'admin.ajax.status',
        'getProfile' => 'admin.ajax.profile',
        'getDividend' => 'admin.ajax.dividend',
    ]);

    // Profile Route...
    Route::controller('/user', 'UserController', [
        'getProfile' => 'admin.user.profile',
        'getPassword' => 'admin.user.password',
        'getAlert' => 'admin.user.alert',
        'getNotice' => 'admin.user.notice',
        'getMessage' => 'admin.user.message',
    ]);

    // Management Route...
    Route::group(['prefix' => '/website'], function() {
        // Carousel Route...
        Route::resource('carousels', 'CarouselController');

        // Member News Route...
        Route::resource('membernews', 'MemberNewsController');

        // News Route...
        Route::resource('news', 'NewsController');

        // Knowledge Route...
        Route::resource('knowledge', 'KnowledgeController');    
    });

    // Admin Route...
    Route::get('/admin/administrator/restore', ['as' => 'admin.administrator.restore', 'uses' => 'AdminController@getRestore']);
    Route::group(['prefix' => '/admin/administrator/{id}'], function() {
        Route::get('erase', ['as' => 'admin.administrator.erase', 'uses' => 'AdminController@getErase']);
        Route::get('forcedelete', ['as' => 'admin.administrator.forcedelete', 'uses' => 'AdminController@getForceDelete']);
        Route::get('undelete', ['as' => 'admin.administrator.undelete', 'uses' => 'AdminController@getUnDelete']);
    });
    Route::resource('/admin/administrator', 'AdminController');

    // Member Route...
    Route::get('/admin/member/inactive', 'MemberController@getInactive');
    Route::get('/admin/member/shareholding', 'MemberController@getShareHolding');
    Route::resource('/admin/member', 'MemberController');
    Route::group(['prefix' => '/admin/member/{id}'], function() {
        Route::get('leave', ['as' => 'admin.member.leave', 'uses' => 'MemberController@getLeave']);
        Route::get('{tab}', ['as' => 'admin.member.tab', 'uses' => 'MemberController@getShowTab']);

        // Share Holding Route...
        Route::get('shareholding/{share}/erase', 'ShareholdingController@getErase');
        Route::resource('shareholding', 'ShareholdingController');

        // Loan Route...
        Route::resource('loan', 'LoanController');
    });

    // Loan Type Route...
    Route::get('/admin/loantype/expire', 'LoanTypeController@getExpire');
    Route::resource('/admin/loantype', 'LoanTypeController');

    // Dividend Route...
    Route::get('/admin/dividend/{id}/erase', 'DividendController@getErase');
    Route::resource('/admin/dividend', 'DividendController');

    // Statistic Route...
    Route::controller('/admin/statistic', 'StatisticController', [
        'getProfile' => 'admin.statistic.index',
    ]);

    // Admin Route...
    Route::controller('/', 'AdminController', [
        'getIndex' => 'admin.index',
        'getUnauthorize' => 'admin.unauthorize',
    ]);
});
