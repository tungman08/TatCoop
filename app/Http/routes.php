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

    // Member Shareholding Billing Route...
    Route::group(['prefix' => '/member/{id}/shareholding/{shareholding_id}/billing/{date}'], function() {
        Route::get('/print', ['as' => 'website.member.shareholding.billing.print', 'uses' => 'MemberController@getPrintShareholdingBilling']);
        Route::get('/pdf', ['as' => 'website.member.shareholding.billing.pdf', 'uses' => 'MemberController@getPdfShareholdingBilling']);
        Route::get('/', ['as' => 'website.member.shareholding.billing', 'uses' => 'MemberController@getShareholdingBilling']);
    });

    // Member Shareholding Route...
    Route::group(['prefix' => '/member/{id}/shareholding'], function() {
		Route::get('/{month}', ['as' => 'website.member.shareholding.show', 'uses' => 'MemberController@getShowShareholding']);
        Route::get('/', ['as' => 'website.member.shareholding', 'uses' => 'MemberController@getShareholding']);
    });

    // Member Shareholding Billing Route...
    Route::group(['prefix' => '/member/{id}/loan/{loan_id}/{payment_id}/billing/{date}'], function() {
        Route::get('/print', ['as' => 'website.member.loan.billing.print', 'uses' => 'MemberController@getPrintLoanBilling']);
        Route::get('/pdf', ['as' => 'website.member.loan.billing.pdf', 'uses' => 'MemberController@getPdfLoanBilling']);
        Route::get('/', ['as' => 'website.member.loan.billing', 'uses' => 'MemberController@getLoanBilling']);
    });

    // Member Loan Route...
    Route::group(['prefix' => '/member/{id}/loan'], function() {
        Route::get('/{loan_id}', ['as' => 'website.member.loan.show', 'uses' => 'MemberController@getShowLoan']);
        Route::get('/', ['as' => 'website.member.loan', 'uses' => 'MemberController@getLoan']);
    });

    // Member Guaruntee Route...
    Route::get('/member/{id}/guaruntee', ['as' => 'website.member.guaruntee', 'uses' => 'MemberController@getGuaruntee']);

    // Member Dividend Route...
    Route::get('/member/{id}/dividend', ['as' => 'website.member.dividend', 'uses' => 'MemberController@getDividend']);

    // Member Route...
    Route::get('/member/unauthorize', ['as' => 'website.member.unauthorize', 'uses' => 'MemberController@getUnauthorize']);
    Route::resource('/member', 'MemberController', ['only' => ['index', 'show', 'edit', 'update']]);

    // Pre Loan Route...
    Route::controller('/loan', 'LoanController');

    // Profile Route...
    Route::controller('/user', 'UserController');

    // Storage Route...
    Route::get('/storage/file/{directory}/{filename}', ['as' => 'website.storage.file', 'uses' => 'StorageController@getFile']);
    Route::get('/storage/download/{directory}/{filename}/{displayname}', ['as' => 'website.storage.download', 'uses' => 'StorageController@getDownload']);

    // Document Route...
    Route::group(['prefix' => '/documents'], function() {
        Route::get('rules', ['as' => 'website.documents.rules', 'uses' => 'DocumentController@getRules']);
        Route::get('rules/{key}', ['as' => 'website.documents.rules.file', 'uses' => 'DocumentController@getRules']);
        Route::get('forms', ['as' => 'website.documents.forms', 'uses' => 'DocumentController@getForms']);
        Route::get('forms/{key}', ['as' => 'website.documents.forms.file', 'uses' => 'DocumentController@getForms']);
        Route::get('{key}', ['as' => 'website.documents.other', 'uses' => 'DocumentController@getOthers']);
        Route::get('/', function() { return redirect('/'); });
    });

    // News Route...
    Route::resource('news', 'NewsController');

    // Knowledge Route...
    Route::resource('knowledges', 'KnowledgeController');

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
        Route::resource('documents', 'DocumentController', ['only' => [ 'index' ]]);     

        // Carousel Route...
        Route::resource('carousels', 'CarouselController');

        // News Route...
        Route::get('news/inactive', ['as' => 'website.news.inactive', 'uses' => 'NewsController@getInactive']);
		Route::get('news/inactive/{id}', ['as' => 'website.news.showinactive', 'uses' => 'NewsController@getShowInactive']);
        Route::post('news/{id}/restore', ['as' => 'website.news.restore', 'uses' => 'NewsController@postRestore']);
        Route::post('news/{id}/forcedelete', ['as' => 'website.news.delete', 'uses' => 'NewsController@postForceDelete']);
        Route::resource('news', 'NewsController');

        // Knowledge Route...
        Route::get('knowledge/inactive', ['as' => 'website.knowledge.inactive', 'uses' => 'KnowledgeController@getInactive']);
		Route::get('knowledge/inactive/{id}', ['as' => 'website.knowledge.showinactive', 'uses' => 'KnowledgeController@getShowInactive']);
        Route::post('knowledge/{id}/restore', ['as' => 'website.knowledge.restore', 'uses' => 'KnowledgeController@postRestore']);
        Route::post('knowledge/{id}/forcedelete', ['as' => 'website.knowledge.delete', 'uses' => 'KnowledgeController@postForceDelete']);
        Route::resource('knowledge', 'KnowledgeController');    
    });

    // Services Route...
    Route::group(['prefix' => '/service'], function() {
        // Member Route...
        Route::get('member/inactive', 'MemberController@getInactive');
        Route::get('member/{id}/leave/{date}', ['as' => 'service.member.leave', 'uses' => 'MemberController@getLeave']);
        Route::post('member/{id}/leave', 'MemberController@postLeave');
        Route::resource('member', 'MemberController');

        // Share Holding Route...
        Route::get('shareholding/member', ['as' => 'service.shareholding.member', 'uses' => 'ShareholdingController@getMember']);
        Route::get('shareholding/{member_id}/{paydate}/show', ['as' => 'service.shareholding.show', 'uses' => 'ShareholdingController@getShow']);
        Route::get('shareholding/{member_id}/{paydate}/detail/{id}', ['as' => 'service.shareholding.detail', 'uses' => 'ShareholdingController@getDetail']);
        Route::get('shareholding/{member_id}/{paydate}/billing/{id}', ['as' => 'service.shareholding.billing', 'uses' => 'ShareholdingController@getBilling']);
        Route::get('shareholding/{member_id}/{paydate}/print/{id}', ['as' => 'service.shareholding.print', 'uses' => 'ShareholdingController@getPrintBilling']);
        Route::get('shareholding/{member_id}/{paydate}/pdf/{id}', ['as' => 'service.shareholding.pdf', 'uses' => 'ShareholdingController@getPdfBilling']);
		Route::post('shareholding/showfiles', 'ShareholdingController@postShowFiles');
        Route::post('shareholding/uploadfile', 'ShareholdingController@postUploadFile');
		Route::post('shareholding/deletefile', 'ShareholdingController@postDeleteFile');
        Route::resource('{member_id}/shareholding', 'ShareholdingController', ['except' => [ 'show' ]]);

        // Loan Route...
        Route::get('loan/member', ['as' => 'service.loan.member', 'uses' => 'LoanController@getMember']);
        Route::get('{member_id}/loan/calsurety', ['as' => 'service.loan.calsurety', 'uses' => 'LoanController@getCalSurety']);
        Route::get('{member_id}/loan/{loantype_id}/create', ['as' => 'service.loan.create', 'uses' => 'LoanController@getCreateLoan']);
        Route::get('{member_id}/loan/create/normal/employee', ['as' => 'service.loan.create.normal.employee', 'uses' => 'NormalLoanController@getCreateEmployeeLoan']);
        Route::get('{member_id}/loan/create/normal/outsider', ['as' => 'service.loan.create.normal.outsider', 'uses' => 'NormalLoanController@getCreateOutsiderLoan']);
        Route::get('{member_id}/loan/create/emerging/employee', ['as' => 'service.loan.create.emerging.employee', 'uses' => 'EmergingLoanController@getCreateEmployeeLoan']);
        Route::get('{member_id}/loan/create/emerging/outsider', ['as' => 'service.loan.create.emerging.outsider', 'uses' => 'EmergingLoanController@getCreateOutsiderLoan']);
        Route::get('{member_id}/loan/create/special/employee', ['as' => 'service.loan.create.special.employee', 'uses' => 'SpecialLoanController@getCreateEmployeeLoan']);
        Route::get('{member_id}/loan/create/special/outsider', ['as' => 'service.loan.create.special.outsider', 'uses' => 'SpecialLoanController@getCreateOutsiderLoan']);
        Route::get('{member_id}/loan/create/refinance/employee', ['as' => 'service.loan.create.refinance.employee', 'uses' => 'RefinanceController@getCreateEmployeeRefinance']);
        Route::get('{member_id}/loan/create/refinance/outsider', ['as' => 'service.loan.create.refinance.outsider', 'uses' => 'RefinanceController@getCreateOutsiderRefinance']);
        Route::get('{member_id}/loan/{loan_id}/sureties/edit', ['as' => 'service.loan.sureties.edit', 'uses' => 'LoanController@getEditSureties']);
        Route::get('{member_id}/loan/debt', ['as' => 'service.loan.debt', 'uses' => 'LoanController@getDebt']);
        Route::get('{member_id}/loan/debt/print', ['as' => 'service.loan.debtprint', 'uses' => 'LoanController@getDebtPrint']);
        Route::get('{member_id}/loan/debt/pdf', ['as' => 'service.loan.debtpdf', 'uses' => 'LoanController@getDebtPdf']);
        Route::post('{member_id}/loan/create/normal/employee', 'NormalLoanController@postCreateEmployeeLoan');
        Route::post('{member_id}/loan/create/normal/outsider', 'NormalLoanController@postCreateOutsiderLoan');
        Route::post('{member_id}/loan/create/emerging/employee', 'EmergingLoanController@postCreateEmployeeLoan');
        Route::post('{member_id}/loan/create/emerging/outsider', 'EmergingLoanController@postCreateOutsiderLoan');
        Route::post('{member_id}/loan/create/special/employee', 'SpecialLoanController@postCreateEmployeeLoan');
        Route::post('{member_id}/loan/create/special/outsider', 'SpecialLoanController@postCreateOutsiderLoan');
        Route::post('{member_id}/loan/create/refinance/employee', 'RefinanceController@getCreateEmployeeRefinance');
        Route::post('{member_id}/loan/create/refinance/outsider', 'RefinanceController@getCreateOutsiderRefinance');
        Route::resource('{member_id}/loan', 'LoanController');
        
        // Payment Route...
        Route::post('loan/payment/uploadfile', 'PaymentController@postUploadFile');
		Route::post('loan/payment/deletefile', 'PaymentController@postDeleteFile');
        Route::group(['prefix' => '{member_id}/loan/{loan_id}'], function () {
            Route::get('payment/calculate', ['as' => 'service.payment.calculate', 'uses' => 'PaymentController@getCalculate']);
            Route::get('payment/close', ['as' => 'service.payment.close', 'uses' => 'PaymentController@getClose']);
            Route::post('payment/close', 'PaymentController@postClose');
            Route::get('payment/billing/{payment_id}/{paydate}', ['as' => 'service.payment.billing', 'uses' => 'PaymentController@getBilling']);
            Route::get('payment/print/{payment_id}/{paydate}', ['as' => 'service.payment.print', 'uses' => 'PaymentController@getPrintBilling']);
            Route::get('payment/pdf/{payment_id}/{paydate}', ['as' => 'service.payment.pdf', 'uses' => 'PaymentController@getPdfBilling']);
            Route::resource('payment', 'PaymentController');    
        });

        // Dividend Route...
        Route::get('dividend/member', ['as' => 'service.dividend.member', 'uses' => 'DividendController@getMember']);
        Route::get('{member_id}/dividend', ['as' => 'service.dividend.member.show', 'uses' => 'DividendController@getMemberDividend']);
        Route::get('{member_id}/dividend/{dividend_id}/edit', ['as' => 'service.dividendmember.edit', 'uses' => 'DividendController@getMemberEdit']);
        Route::post('{member_id}/dividend/{dividend_id}/edit', 'DividendController@postMemberUpdate');  

        // Quaruntee Route...
        Route::get('guaruntee/member', ['as' => 'service.guaruntee.member', 'uses' => 'GuarunteeController@getMember']);
        Route::resource('{member_id}/guaruntee', 'GuarunteeController');
    });

    // Co-op Route...
    Route::group(['prefix' => '/coop'], function() {
        // Loan List Route...
        Route::get('loanlist', ['as' => 'coop.loan.loanlist', 'uses' => 'LoanController@getLoanList']);

        // Routine Shareholding Payment Route...
        Route::post('routine/shareholding/detail', 'RoutineShareholdingController@saveDetail');
        Route::get('routine/shareholding/detail/{id}/edit', ['as' => 'coop.routine.shareholding.detail.edit', 'uses' => 'RoutineShareholdingController@editDetail']);
        Route::put('routine/shareholding/detail/{id}', 'RoutineShareholdingController@updateDetail');
        Route::delete('routine/shareholding/detail/{id}', 'RoutineShareholdingController@deleteDetail');
        Route::post('routine/shareholding/{id}/save', 'RoutineShareholdingController@save');
        Route::post('routine/shareholding/{id}/report', 'RoutineShareholdingController@report');
        Route::resource('routine/shareholding', 'RoutineShareholdingController', ['only' => [ 'index', 'show' ]]);

        // Routine Loan Payment Route...
        Route::post('routine/payment/detail', 'RoutinePaymentController@saveDetail');
        Route::get('routine/payment/detail/{id}/edit', ['as' => 'coop.routine.payment.detail.edit', 'uses' => 'RoutinePaymentController@editDetail']);
        Route::put('routine/payment/detail/{id}', 'RoutinePaymentController@updateDetail');
        Route::delete('routine/payment/detail/{id}', 'RoutinePaymentController@deleteDetail');
        Route::post('routine/payment/{id}/save', 'RoutinePaymentController@save');
        Route::post('routine/payment/{id}/report', 'RoutinePaymentController@report');
        Route::resource('routine/payment', 'RoutinePaymentController', ['only' => [ 'index', 'show' ]]);

        // Routine Available Loan Route...
        Route::get('available/loan', ['as' => 'coop.available.loan', 'uses' => 'LoanController@getAvailable']);

        // Routine Available Bailsman Route...
        Route::get('available/bailsman', ['as' => 'coop.available.bailsman', 'uses' => 'BailsmanController@getAvailable']);
    });

    // Database Route...
    Route::group(['prefix' => '/database'], function() {
        // Loan Type Route...
        Route::get('loantype/{id}/finished', ['as' => 'database.loantype.finished', 'uses' => 'LoanTypeController@getFinished']);
        Route::get('loantype/expired', ['as' => 'database.loantype.expired', 'uses' => 'LoanTypeController@getExpired']);
        Route::get('loantype/expired/{id}', ['as' => 'database.loantype.expired.detail', 'uses' => 'LoanTypeController@getExpiredDetail']);
        Route::get('loantype/inactive', ['as' => 'database.loantype.inactive', 'uses' => 'LoanTypeController@getInactive']);
        Route::post('loantype/{id}/forcedelete', 'LoanTypeController@postForceDelete');
        Route::post('loantype/{id}/restore', 'LoanTypeController@postRestore');       
        Route::resource('loantype', 'LoanTypeController');

        // Bailsman Route...
        Route::resource('bailsman', 'BailsmanController', ['only' => [ 'index', 'edit', 'update' ]]);

        // Dividend Route...
        Route::resource('dividend', 'DividendController', ['except' => [ 'show' ]]);

        // Billing Route...
        Route::resource('billing', 'BillingController', ['only' => [ 'index', 'create', 'store', 'edit', 'update' ]]);        
    });

    // Admin Route...
    Route::group(['prefix' => '/admin'], function() {
        // Admin Accounts Route...
        Route::get('administrator/{id}/delete', ['as' => 'admin.administrator.delete', 'uses' => 'AdminController@getDelete']);
        Route::get('administrator/inactive', ['as' => 'admin.administrator.inactive', 'uses' => 'AdminController@getInactive']);
        Route::post('administrator/{id}/forcedelete', 'AdminController@postForceDelete');
        Route::post('administrator/{id}/restore', 'AdminController@postRestore');
        Route::resource('administrator', 'AdminController');

        //Board Account Route...
        Route::get('board/{id}/delete', ['as' => 'admin.board.delete', 'uses' => 'BoardController@getDelete']);
        Route::get('board/inactive', ['as' => 'admin.board.inactive', 'uses' => 'BoardController@getInactive']);
        Route::post('board/{id}/forcedelete', 'BoardController@postForceDelete');
        Route::post('board/{id}/restore', 'BoardController@postRestore');
        Route::resource('board', 'BoardController');

        // User Account Route...
        Route::resource('account', 'AccountController', ['only' => [ 'index', 'show', 'edit', 'update' ]]);

        // Slotmachine Route...
        Route::get('reward/slotmachine', ['as' => 'admin.reward.play', 'uses' => 'RewardController@getSlotmachine']);
        Route::post('reward/winners', 'RewardController@postWinners');
        Route::post('reward/shuffle', 'RewardController@postShuffle');
        Route::post('reward/savewinner', 'RewardController@postSavewinner');
        Route::resource('reward', 'RewardController', ['only' => [ 'index', 'show', 'destroy' ]]);

        // Reports Route...
        Route::post('report/export', 'ReportController@postExport'); 
        Route::get('report', ['as' => 'admin.report.index', 'uses' => 'ReportController@getIndex']);

        // Statistic Route...
        Route::controller('statistic', 'StatisticController', [
            'getProfile' => 'admin.statistic.index',
        ]);
    });

    // Admin Route...
    Route::controller('/', 'HomeController');
});
