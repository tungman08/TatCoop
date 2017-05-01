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
Route::group(['domain' => 'www.' . env('APP_DOMAIN'),
    'namespace' => 'Website',
    'middleware' => 'locale:th'], function () {

    // Ajax Route...
    Route::controller('/ajax', 'AjaxController');

    // Auth Route...
    Route::get('/auth/verify/{token}', ['as' => 'website.auth.verify', 'uses' => 'AuthController@getVerify']); // Verify Route...
    Route::controller('/auth', 'AuthController');

    // Background Route...
    Route::get('/background/{photo}', ['as' => 'website.background', 'uses' => 'HomeController@getBackground']);

    // Password Route...
    Route::group(['prefix' => '/password'], function() {
        Route::get('email', ['as' => 'website.password.email', 'uses' => 'PasswordController@getEmail']);
        Route::get('reset/{token}', ['as' => 'website.password.reset', 'uses' => 'PasswordController@getReset']);
        Route::post('email', 'PasswordController@postEmail');
        Route::post('reset', 'PasswordController@postReset');
    });

    // Member Route...
    Route::resource('/member', 'MemberController', ['only' => ['index', 'edit', 'update']]);

    // Billing Route...
    Route::group(['prefix' => '/member/shareholding/billing'], function() {
        Route::get('{date}/print', ['as' => 'website.member.shareholding.billing.print', 'uses' => 'MemberController@getPrintBilling']);
        Route::get('{date}/pdf', ['as' => 'website.member.shareholding.billing.pdf', 'uses' => 'MemberController@getPdfBilling']);
        Route::get('{date}', ['as' => 'website.member.shareholding.billing', 'uses' => 'MemberController@getBilling']);
    });

    // Pre Loan Route...
    Route::controller('/loan', 'LoanController');

    // Profile Route...
    Route::controller('/user', 'UserController');

    // Carousel Route...
    Route::get('/carousel/{image}', ['as' => 'website.carousel', 'uses' => 'CarouselController@getCarousel']);

    // Attachment Route...
    Route::get('/attachment/{file}', ['as' => 'website.attachment', 'uses' => 'AttachmentController@getAttachment']);

    // Document Route...
    Route::get('/document/{document}/{display}', ['as' => 'website.document.download', 'uses' => 'DocumentController@getDownloadDocument']);
    Route::get('/document/{document}', ['as' => 'website.document', 'uses' => 'DocumentController@getDocument']);
    Route::group(['prefix' => '/documents'], function() {
        Route::get('rules', ['as' => 'website.documents.rules', 'uses' => 'DocumentController@getRules']);
        Route::get('rules/{key}', ['as' => 'website.documents.rules.file', 'uses' => 'DocumentController@getRules']);
        Route::get('forms', ['as' => 'website.documents.forms', 'uses' => 'DocumentController@getForms']);
        Route::get('forms/{key}', ['as' => 'website.documents.forms.file', 'uses' => 'DocumentController@getForms']);
        Route::get('{key}', ['as' => 'website.documents.other', 'uses' => 'DocumentController@getOthers']);
    });

    // News Route...
    Route::resource('news', 'NewsController');

    // Knowledge Route...
    Route::resource('knowledges', 'KnowledgeController');

    // Homepage Route...
    Route::controller('/', 'HomeController');
});

/* ====================================================================== */
// Admin Route...
/* ====================================================================== */
Route::group(['domain' => 'admin.' . env('APP_DOMAIN'),
    'namespace' => 'Admin',
    'middleware' => 'locale:th'], function () {

    // Auth Route...
    Route::controller('/auth', 'AuthController');

    // Ajax Route...
    Route::controller('/ajax', 'AjaxController');

    // Background Route...
    Route::get('/background/{photo}', ['as' => 'website.background', 'uses' => 'BackgroundController@getBackground']);

    // Carousel Route...
    Route::get('/carousel/{image}', ['as' => 'admin.carousel', 'uses' => 'CarouselController@getCarousel']);

    // Profile Route...
    Route::controller('/user', 'UserController');

    // Management Route...
    Route::group(['prefix' => '/website'], function() {
        // Documents Route...
        Route::resource('documents', 'DocumentController', ['only' => [ 'index' ]]);     

        // Carousel Route...
        Route::resource('carousels', 'CarouselController');

        // News Route...
        Route::get('news/inactive', ['as' => 'website.news.inactive', 'uses' => 'NewsController@getInactive']);
        Route::get('news/{id}/restore', ['as' => 'website.news.restore', 'uses' => 'NewsController@postRestore']);
        Route::get('news/{id}/forcedelete', ['as' => 'website.news.delete', 'uses' => 'NewsController@postForceDelete']);
        Route::resource('news', 'NewsController');

        // Knowledge Route...
        Route::get('knowledge/inactive', ['as' => 'website.knowledge.inactive', 'uses' => 'KnowledgeController@getInactive']);
        Route::get('knowledge/{id}/restore', ['as' => 'website.knowledge.restore', 'uses' => 'KnowledgeController@postRestore']);
        Route::get('knowledge/{id}/forcedelete', ['as' => 'website.knowledge.delete', 'uses' => 'KnowledgeController@postForceDelete']);
        Route::resource('knowledge', 'KnowledgeController');    
    });

    // Services Route...
    Route::group(['prefix' => '/service'], function() {
        // Member Route...
        Route::get('member/inactive', 'MemberController@getInactive');
        Route::get('/member/{id}/leave', ['as' => 'admin.member.leave', 'uses' => 'MemberController@getLeave']);
        Route::post('/member/{id}/leave', 'MemberController@postLeave');
        Route::resource('member', 'MemberController');

        // Share Holding Route...
        Route::get('shareholding/member', ['as' => 'service.shareholding.member', 'uses' => 'ShareholdingController@getMember']);
        Route::get('shareholding/autoshareholding', ['as' => 'service.shareholding.auto', 'uses' => 'ShareholdingController@getAutoShareholding']);
        Route::post('shareholding/autoshareholding', 'ShareholdingController@postAutoShareholding');
        Route::resource('{member_id}/shareholding', 'ShareholdingController');

        // Loan Route...
        Route::get('loan/member', ['as' => 'service.loan.member', 'uses' => 'LoanController@getMember']);
        Route::get('{member_id}/loan/{loantype_id}/create', ['as' => 'service.loan.create', 'uses' => 'LoanController@getCreateLoan']);
        Route::get('{member_id}/loan/{loantype_id}/create/normal/employee', ['as' => 'service.loan.create.normal.employee', 'uses' => 'LoanController@getCreateNormalEmployeeLoan']);
        Route::get('{member_id}/loan/{loantype_id}/create/normal/outsider', ['as' => 'service.loan.create.normal.outsider', 'uses' => 'LoanController@getCreateNormalOutsiderLoan']);
        Route::get('{member_id}/loan/{loantype_id}/create/emerging', ['as' => 'service.loan.create.emerging', 'uses' => 'LoanController@getCreateEmergingLoan']);
        Route::get('{member_id}/loan/{loantype_id}/create/special', ['as' => 'service.loan.create.special', 'uses' => 'LoanController@getCreateSpecialLoan']);
        Route::resource('{member_id}/loan', 'LoanController');

        // Payment Route...
        Route::resource('{member_id}/payment', 'PaymentController');

        // Dividend Route...
        Route::get('dividend/member', ['as' => 'service.dividend.member', 'uses' => 'DividendController@getMember']);
        Route::resource('{member_id}/dividend', 'DividendController');

        // Quaruntee Route...
        Route::get('guaruntee/member', ['as' => 'service.guaruntee.member', 'uses' => 'GuarunteeController@getMember']);
        Route::resource('{member_id}/guaruntee', 'GuarunteeController');
    });

    // Admin Route...
    Route::group(['prefix' => '/admin'], function() {
        // Admin Accounts Route...
        Route::get('administrator/{id}/delete', ['as' => 'admin.administrator.delete', 'uses' => 'AdminController@getDelete']);
        Route::get('administrator/inactive', ['as' => 'admin.administrator.inactive', 'uses' => 'AdminController@getInactive']);
        Route::post('administrator/{id}/forcedelete', 'AdminController@postForceDelete');
        Route::post('administrator/{id}/restore', 'AdminController@postRestore');
        Route::resource('administrator', 'AdminController', ['except' => [ 'show' ]]);

        // User Account Route...
        Route::resource('account', 'AccountController', ['only' => [ 'index' ]]);

        // Loan Type Route...
        Route::get('loantype/{id}/finished', ['as' => 'admin.loantype.finished', 'uses' => 'LoanTypeController@getFinished']);
        Route::get('loantype/expired', ['as' => 'admin.loantype.expired', 'uses' => 'LoanTypeController@getExpired']);
        Route::get('loantype/inactive', 'LoanTypeController@getInactive');
        Route::post('loantype/{id}/forcedelete', 'LoanTypeController@postForceDelete');
        Route::post('loantype/{id}/restore', 'LoanTypeController@postRestore');        
        Route::resource('loantype', 'LoanTypeController');

        // Dividend Route...
        Route::get('dividend/{id}/export', 'DividendController@getExport');
        Route::resource('dividend', 'DividendController');

        // Billing Route...
        Route::resource('billing', 'BillingController', ['only' => [ 'index', 'edit', 'update' ]]);

        // Statistic Route...
        Route::controller('statistic', 'StatisticController', [
            'getProfile' => 'admin.statistic.index',
        ]);
    });

    // Admin Route...
    Route::controller('/', 'HomeController');
});
