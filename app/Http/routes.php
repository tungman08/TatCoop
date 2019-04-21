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
        Route::get('/email', ['as' => 'website.password.email', 'uses' => 'PasswordController@getEmail']);
        Route::get('/reset/{token}', ['as' => 'website.password.reset', 'uses' => 'PasswordController@getReset']);
        Route::post('/email', 'PasswordController@postEmail');
        Route::post('/reset', 'PasswordController@postReset');
    });

    //Member Route...
    Route::group(['prefix' => '/member'], function() {
        // Shareholding Route...
        Route::get('/shareholding/{shareholding_id}/billing/{date}/print', ['as' => 'website.shareholding.print', 'uses' => 'ShareholdingController@getPrint']);
        Route::get('/shareholding/{shareholding_id}/billing/{date}/pdf', ['as' => 'website.shareholding.pdf', 'uses' => 'ShareholdingController@getPdf']);
        Route::get('/shareholding/{shareholding_id}/billing/{date}', ['as' => 'website.shareholding.billing', 'uses' => 'ShareholdingController@getBilling']);
        Route::resource('/shareholding', 'ShareholdingController', ['only' => [ 'index', 'show' ]]);

        // Loan Route...
        Route::get('/loan/{loan_id}/{payment_id}/billing/{date}/print', ['as' => 'website.member.loan.print', 'uses' => 'LoanController@getPrint']);
        Route::get('/loan/{loan_id}/{payment_id}/billing/{date}/pdf', ['as' => 'website.member.loan.pdf', 'uses' => 'LoanController@getPdf']);
        Route::get('/loan/{loan_id}/{payment_id}/billing/{date}', ['as' => 'website.member.loan.billing', 'uses' => 'LoanController@getBilling']);
        Route::resource('/loan', 'LoanController', ['only' => [ 'index', 'show' ]]);

        // Guaruntee Route...
        Route::resource('/guaruntee', 'GuarunteeController', ['only' => [ 'index' ]]);

        // Dividend Route...
        Route::resource('/dividend', 'DividendController', ['only' => [ 'index' ]]);

        // Profile Route...
        Route::controller('/profile', 'ProfileController');

        // Member Route...
        Route::resource('/', 'MemberController', ['only' => [ 'index' ]]);
    });

    // Pre Loan Route...
    Route::get('/loan/calculate', ['as' => 'website.loan.calculate', 'uses' => 'LoanController@getCalculate']);


    // Storage Route...
    Route::get('/storage/file/{directory}/{filename}', ['as' => 'website.storage.file', 'uses' => 'StorageController@getFile']);
    Route::get('/storage/download/{directory}/{filename}/{displayname}', ['as' => 'website.storage.download', 'uses' => 'StorageController@getDownload']);

    // Document Route...
    Route::group(['prefix' => '/documents'], function() {
        Route::get('/rules', ['as' => 'website.documents.rules', 'uses' => 'DocumentController@getRules']);
        Route::get('/rules/{key}', ['as' => 'website.documents.rules.file', 'uses' => 'DocumentController@getRules']);
        Route::get('/forms', ['as' => 'website.documents.forms', 'uses' => 'DocumentController@getForms']);
        Route::get('/forms/{key}', ['as' => 'website.documents.forms.file', 'uses' => 'DocumentController@getForms']);
        Route::get('/{key}', ['as' => 'website.documents.other', 'uses' => 'DocumentController@getOthers']);
        Route::get('/', function() { return redirect('/'); });
    });

    // News Route...
    Route::resource('/news', 'NewsController', ['only' => [ 'index', 'show' ]]);

    // Knowledge Route...
    Route::resource('/knowledges', 'KnowledgeController', ['only' => [ 'index', 'show' ]]);

    // Homepage Route...
    Route::resource('/', 'HomeController', ['only' => [ 'index' ]]);  
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
    Route::get('/background/{photo}', ['as' => 'admin.background', 'uses' => 'BackgroundController@getBackground']);

    // Carousel Route...
    Route::get('/carousel/{image}', ['as' => 'admin.carousel', 'uses' => 'CarouselController@getCarousel']);

    // Profile Route...
    Route::controller('/user', 'UserController');

    // Management Route...
    Route::group(['prefix' => '/website'], function() {
        // Documents Route...
        Route::resource('/documents', 'DocumentController', ['only' => [ 'index' ]]);     

        // Carousel Route...
        Route::resource('/carousels', 'CarouselController');

        // News Route...
        Route::get('/news/inactive', ['as' => 'website.news.inactive', 'uses' => 'NewsController@getInactive']);
		Route::get('/news/inactive/{id}', ['as' => 'website.news.showinactive', 'uses' => 'NewsController@getShowInactive']);
        Route::post('/news/{id}/restore', ['as' => 'website.news.restore', 'uses' => 'NewsController@postRestore']);
        Route::post('/news/{id}/forcedelete', ['as' => 'website.news.delete', 'uses' => 'NewsController@postForceDelete']);
        Route::resource('/news', 'NewsController');

        // Knowledge Route...
        Route::get('/knowledge/inactive', ['as' => 'website.knowledge.inactive', 'uses' => 'KnowledgeController@getInactive']);
		Route::get('/knowledge/inactive/{id}', ['as' => 'website.knowledge.showinactive', 'uses' => 'KnowledgeController@getShowInactive']);
        Route::post('/knowledge/{id}/restore', ['as' => 'website.knowledge.restore', 'uses' => 'KnowledgeController@postRestore']);
        Route::post('/knowledge/{id}/forcedelete', ['as' => 'website.knowledge.delete', 'uses' => 'KnowledgeController@postForceDelete']);
        Route::resource('/knowledge', 'KnowledgeController');    
    });

    // Services Route...
    Route::group(['prefix' => '/service'], function() {
        // Member Route...
        Route::get('/member/inactive', ['as' => 'service.member.inactive', 'uses' => 'MemberController@getInactive']);
        Route::get('/member/{id}/leave/{date}', ['as' => 'service.member.leave', 'uses' => 'MemberController@getLeave']);
        Route::post('/member/{id}/leave', 'MemberController@postLeave');
        Route::resource('/member', 'MemberController', ['except' => [ 'destroy' ]]);

        // Shareholding Route...
        Route::group(['prefix' => '/shareholding'], function() {
            Route::get('/member', ['as' => 'service.shareholding.member', 'uses' => 'ShareholdingController@getMember']);
            Route::get('/member/{member_id}/{paydate}/show', ['as' => 'service.shareholding.show', 'uses' => 'ShareholdingController@getShow']);
            Route::get('/member/{member_id}/{paydate}/detail/{id}', ['as' => 'service.shareholding.detail', 'uses' => 'ShareholdingController@getDetail']);
            Route::get('/member/{member_id}/{paydate}/billing/{id}', ['as' => 'service.shareholding.billing', 'uses' => 'ShareholdingController@getBilling']);
            Route::get('/member/{member_id}/{paydate}/print/{id}', ['as' => 'service.shareholding.print', 'uses' => 'ShareholdingController@getPrintBilling']);
            Route::get('/member/{member_id}/{paydate}/pdf/{id}', ['as' => 'service.shareholding.pdf', 'uses' => 'ShareholdingController@getPdfBilling']);
            Route::post('/showfiles', 'ShareholdingController@postShowFiles');
            Route::post('/uploadfile', 'ShareholdingController@postUploadFile');
            Route::post('/deletefile', 'ShareholdingController@postDeleteFile');
            Route::get('/member/{member_id}', ['as' => 'service.shareholding.index', 'uses' => 'ShareholdingController@index']);
            Route::get('/member/{member_id}/create', ['as' => 'service.shareholding.create', 'uses' => 'ShareholdingController@create']);
            Route::post('/member/{member_id}', 'ShareholdingController@store');
            Route::get('/member/{member_id}/{id}/edit', ['as' => 'service.shareholding.edit', 'uses' => 'ShareholdingController@edit']);
            Route::put('/member/{member_id}/{id}', 'ShareholdingController@update');
            Route::delete('/member/{member_id}/{id}', 'ShareholdingController@destroy');
        });

        // Loan Route...
        Route::group(['prefix' => '/loan'], function() {    
            Route::get('/member', ['as' => 'service.loan.member', 'uses' => 'LoanController@getMember']);
            Route::get('/member/{member_id}/debt', ['as' => 'service.loan.debt', 'uses' => 'LoanController@getDebt']);
            Route::get('/member/{member_id}/debt/print', ['as' => 'service.loan.debtprint', 'uses' => 'LoanController@getDebtPrint']);
            Route::get('/member/{member_id}/debt/pdf', ['as' => 'service.loan.debtpdf', 'uses' => 'LoanController@getDebtPdf']);
            Route::get('/member/{member_id}/create/normal/employee', ['as' => 'service.loan.create.normal.employee', 'uses' => 'NormalLoanController@getCreateEmployeeLoan']);
            Route::get('/member/{member_id}/create/normal/outsider', ['as' => 'service.loan.create.normal.outsider', 'uses' => 'NormalLoanController@getCreateOutsiderLoan']);
            Route::get('/member/{member_id}/create/emerging/employee', ['as' => 'service.loan.create.emerging.employee', 'uses' => 'EmergingLoanController@getCreateEmployeeLoan']);
            Route::get('/member/{member_id}/create/emerging/outsider', ['as' => 'service.loan.create.emerging.outsider', 'uses' => 'EmergingLoanController@getCreateOutsiderLoan']);
            Route::get('/member/{member_id}/create/special/employee', ['as' => 'service.loan.create.special.employee', 'uses' => 'SpecialLoanController@getCreateEmployeeLoan']);
            Route::get('/member/{member_id}/create/special/outsider', ['as' => 'service.loan.create.special.outsider', 'uses' => 'SpecialLoanController@getCreateOutsiderLoan']);
            Route::get('/member/{member_id}/{loantype_id}/create', ['as' => 'service.loan.create', 'uses' => 'LoanController@getCreateLoan']);
            Route::get('/member/{member_id}/{loan_id}/sureties/edit', ['as' => 'service.loan.sureties.edit', 'uses' => 'LoanController@getEditSureties']);
            Route::get('/member/{member_id}/calsurety', ['as' => 'service.loan.calsurety', 'uses' => 'LoanController@getCalSurety']);
            Route::post('/member/{member_id}/create/normal/employee', 'NormalLoanController@postCreateEmployeeLoan');
            Route::post('/member/{member_id}/create/normal/outsider', 'NormalLoanController@postCreateOutsiderLoan');
            Route::post('/member/{member_id}/create/emerging/employee', 'EmergingLoanController@postCreateEmployeeLoan');
            Route::post('/member/{member_id}/create/emerging/outsider', 'EmergingLoanController@postCreateOutsiderLoan');
            Route::post('/member/{member_id}/create/special/employee', 'SpecialLoanController@postCreateEmployeeLoan');
            Route::post('/member/{member_id}/create/special/outsider', 'SpecialLoanController@postCreateOutsiderLoan');
            Route::post('/showfiles', 'LoanController@postShowFiles');
            Route::post('/uploadfile', 'LoanController@postUploadFile');
            Route::post('/deletefile', 'LoanController@postDeleteFile');
            Route::get('/member/{member_id}', ['as' => 'service.loan.index', 'uses' => 'LoanController@index']);
            Route::get('/member/{member_id}/create', ['as' => 'service.loan.create', 'uses' => 'LoanController@create']);
            Route::post('/member/{member_id}', 'LoanController@store');
            Route::get('/member/{member_id}/{id}', ['as' => 'service.loan.show', 'uses' => 'LoanController@show']);
            Route::get('/member/{member_id}/{id}/edit', ['as' => 'service.loan.edit', 'uses' => 'LoanController@edit']);
            Route::put('/member/{member_id}/{id}', 'LoanController@update');
            Route::delete('/member/{member_id}/{id}', 'LoanController@destroy');

            // Payment Route...
            Route::get('/member/payment/{loan_id}/calculate', ['as' => 'service.payment.calculate', 'uses' => 'PaymentController@getCalculate']);
            Route::get('/member/payment/{loan_id}/close', ['as' => 'service.payment.close', 'uses' => 'PaymentController@getClose']);
            Route::post('/member/payment/{loan_id}/close', 'PaymentController@postClose');
            Route::get('/member/payment/billing/{payment_id}/{paydate}', ['as' => 'service.payment.billing', 'uses' => 'PaymentController@getBilling']);
            Route::get('/member/payment/billing/print/{payment_id}/{paydate}', ['as' => 'service.payment.print', 'uses' => 'PaymentController@getPrintBilling']);
            Route::get('/member/payment/billing/pdf/{payment_id}/{paydate}', ['as' => 'service.payment.pdf', 'uses' => 'PaymentController@getPdfBilling']);
            Route::post('/payment/uploadfile', 'PaymentController@postUploadFile');
            Route::post('/payment/deletefile', 'PaymentController@postDeleteFile');
            Route::get('/member/payment/{loan_id}/create', ['as' => 'service.payment.create', 'uses' => 'PaymentController@create']);
            Route::post('/member/payment/{loan_id}', 'PaymentController@store');
            Route::get('/member/payment/{loan_id}/{id}', ['as' => 'service.payment.show', 'uses' => 'PaymentController@show']);
            Route::get('/member/payment/{loan_id}/{id}/edit', ['as' => 'service.payment.edit', 'uses' => 'PaymentController@edit']);
            Route::put('/member/payment/{loan_id}/{id}', 'PaymentController@update');
            Route::delete('/member/payment/{loan_id}/{id}', 'PaymentController@destroy');
        });

        // Dividend Route...
        Route::group(['prefix' => '/dividend'], function() {
            Route::get('/member', ['as' => 'service.dividend.member', 'uses' => 'DividendController@getMember']);
            Route::get('/member/{member_id}', ['as' => 'service.dividend.member.show', 'uses' => 'DividendController@getMemberDividend']);
            Route::get('/member/{member_id}/{dividend_id}/edit', ['as' => 'service.dividendmember.edit', 'uses' => 'DividendController@getMemberEdit']);
            Route::post('/member/{member_id}/{dividend_id}/update', 'DividendController@postMemberUpdate');  
        });  

        // Guaruntee Route...
        Route::group(['prefix' => '/guaruntee'], function() {
            Route::get('/member', ['as' => 'service.guaruntee.member', 'uses' => 'GuarunteeController@getMember']);
            Route::get('/member/{member_id}', ['as' => 'service.guaruntee.index', 'uses' => 'GuarunteeController@index']);
        });
    });

    // Co-op Route...
    Route::group(['prefix' => '/coop'], function() {
        // Loan List Route...
        Route::get('/loanlist', ['as' => 'coop.loan.loanlist', 'uses' => 'LoanController@getLoanList']);

        // Routine Shareholding Payment Route...
        Route::post('/routine/shareholding/detail', 'RoutineShareholdingController@saveDetail');
        Route::get('/routine/shareholding/detail/{id}/edit', ['as' => 'coop.routine.shareholding.detail.edit', 'uses' => 'RoutineShareholdingController@editDetail']);
        Route::put('/routine/shareholding/detail/{id}', 'RoutineShareholdingController@updateDetail');
        Route::delete('/routine/shareholding/detail/{id}', 'RoutineShareholdingController@deleteDetail');
        Route::post('/routine/shareholding/{id}/save', 'RoutineShareholdingController@save');
        Route::post('/routine/shareholding/{id}/report', 'RoutineShareholdingController@report');
        Route::resource('/routine/shareholding', 'RoutineShareholdingController', ['only' => [ 'index', 'show' ]]);

        // Routine Loan Payment Route...
        Route::post('/routine/payment/detail', 'RoutinePaymentController@saveDetail');
        Route::get('/routine/payment/detail/{id}/edit', ['as' => 'coop.routine.payment.detail.edit', 'uses' => 'RoutinePaymentController@editDetail']);
        Route::put('/routine/payment/detail/{id}', 'RoutinePaymentController@updateDetail');
        Route::delete('/routine/payment/detail/{id}', 'RoutinePaymentController@deleteDetail');
        Route::post('/routine/payment/{id}/save', 'RoutinePaymentController@save');
        Route::post('/routine/payment/{id}/report', 'RoutinePaymentController@report');
        Route::resource('/routine/payment', 'RoutinePaymentController', ['only' => [ 'index', 'show' ]]);

        // Routine Available Loan Route...
        Route::get('/available/loan', ['as' => 'coop.available.loan', 'uses' => 'LoanController@getAvailable']);

        // Routine Available Bailsman Route...
        Route::get('/available/bailsman', ['as' => 'coop.available.bailsman', 'uses' => 'BailsmanController@getAvailable']);
    });

    // Database Route...
    Route::group(['prefix' => '/database'], function() {
        // Loan Type Route...
        Route::get('/loantype/{id}/finished', ['as' => 'database.loantype.finished', 'uses' => 'LoanTypeController@getFinished']);
        Route::get('/loantype/expired', ['as' => 'database.loantype.expired', 'uses' => 'LoanTypeController@getExpired']);
        Route::get('/loantype/expired/{id}', ['as' => 'database.loantype.expired.detail', 'uses' => 'LoanTypeController@getExpiredDetail']);
        Route::get('/loantype/inactive', ['as' => 'database.loantype.inactive', 'uses' => 'LoanTypeController@getInactive']);
        Route::post('/loantype/{id}/forcedelete', 'LoanTypeController@postForceDelete');
        Route::post('/loantype/{id}/restore', 'LoanTypeController@postRestore');       
        Route::resource('/loantype', 'LoanTypeController');

        // Bailsman Route...
        Route::resource('/bailsman', 'BailsmanController', ['only' => [ 'index', 'edit', 'update' ]]);

        // Dividend Route...
        Route::resource('/dividend', 'DividendController', ['except' => [ 'show' ]]);

        // Billing Route...
        Route::resource('/billing', 'BillingController', ['except' => [ 'show', 'destroy' ]]);     
        
        // Prefix Route...
        Route::resource('/prefix', 'PrefixController', ['except' => [ 'show', 'destroy' ]]);     
    });

    // Admin Route...
    Route::group(['prefix' => '/admin'], function() {
        // Admin Accounts Route...
        Route::get('/administrator/{id}/delete', ['as' => 'admin.administrator.delete', 'uses' => 'AdminController@getDelete']);
        Route::get('/administrator/inactive', ['as' => 'admin.administrator.inactive', 'uses' => 'AdminController@getInactive']);
        Route::post('/administrator/{id}/forcedelete', 'AdminController@postForceDelete');
        Route::post('/administrator/{id}/restore', 'AdminController@postRestore');
        Route::resource('/administrator', 'AdminController');

        //Board Account Route...
        Route::get('/board/{id}/delete', ['as' => 'admin.board.delete', 'uses' => 'BoardController@getDelete']);
        Route::get('/board/inactive', ['as' => 'admin.board.inactive', 'uses' => 'BoardController@getInactive']);
        Route::post('/board/{id}/forcedelete', 'BoardController@postForceDelete');
        Route::post('/board/{id}/restore', 'BoardController@postRestore');
        Route::resource('/board', 'BoardController');

        // User Account Route...
        Route::resource('/account', 'AccountController', ['only' => [ 'index', 'show', 'edit', 'update' ]]);

        // Slotmachine Route...
        Route::get('/reward/slotmachine', ['as' => 'admin.reward.play', 'uses' => 'RewardController@getSlotmachine']);
        Route::post('/reward/winners', 'RewardController@postWinners');
        Route::post('/reward/shuffle', 'RewardController@postShuffle');
        Route::post('/reward/savewinner', 'RewardController@postSavewinner');
        Route::resource('/reward', 'RewardController', ['only' => [ 'index', 'show', 'destroy' ]]);

        // Reports Route...
        Route::post('/report/export', 'ReportController@postExport'); 
        Route::get('/report', ['as' => 'admin.report.index', 'uses' => 'ReportController@getIndex']);

        // Statistic Route...
        Route::controller('/statistic', 'StatisticController', [
            'getProfile' => 'admin.statistic.index',
        ]);
    });

    // Admin Route...
    Route::controller('/', 'HomeController');
});
