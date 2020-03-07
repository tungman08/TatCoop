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

    // Verification Route...
    Route::get('/auth/verify/{token}', ['as' => 'website.auth.verify', 'uses' => 'AuthController@getVerify']); 

    // Auth Route...
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

        // Cash flow Route...
        Route::get('/home/cashflow/{year}/print', ['as' => 'website.cashflow.print', 'uses' => 'CashflowController@getPrintCashflow']);
        Route::get('/home/cashflow/{year}/pdf', ['as' => 'website.cashflow.pdf', 'uses' => 'CashflowController@getPrintPdfCashflow']);
        Route::resource('/home/cashflow', 'CashflowController', ['only' => [ 'show' ]]);

        // Member Route...
        Route::resource('/home', 'MemberController', ['only' => [ 'index' ]]);
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
        Route::get('/member/{id}/cashflow/{year}/print', ['as' => 'service.member.cashflow.print', 'uses' => 'MemberController@getPrintCashflow']);
        Route::get('/member/{id}/cashflow/{year}/pdf', ['as' => 'service.member.cashflow.pdf', 'uses' => 'MemberController@getPrintPdfCashflow']);
        Route::get('/member/{id}/cashflow/{year}', ['as' => 'service.member.cashflow', 'uses' => 'MemberController@getCashflow']);
        Route::resource('/member', 'MemberController', ['except' => [ 'destroy' ]]);

        // Share Holding Route...
        Route::post('/shareholding/showfiles', 'ShareholdingController@postShowFiles');
        Route::post('/shareholding/uploadfile', 'ShareholdingController@postUploadFile');
        Route::post('/shareholding/deletefile', 'ShareholdingController@postDeleteFile');
        Route::get('/shareholding/member/{member_id}/edit', ['as' => 'service.shareholding.adjust', 'uses' => 'ShareholdingController@getAdjust']);
        Route::put('/shareholding/member/{member_id}', 'ShareholdingController@putAdjust');
        Route::get('/shareholding/member/{member_id}/payment/month/{pay_date}', ['as' => 'service.shareholding.month', 'uses' => 'ShareholdingController@getMonth']);
        Route::get('/shareholding/member/{member_id}/payment/month/{pay_date}/billing/{id}', ['as' => 'service.shareholding.billing', 'uses' => 'ShareholdingController@getBilling']);
        Route::get('/shareholding/member/{member_id}/payment/month/{pay_date}/print/{id}', ['as' => 'service.shareholding.print', 'uses' => 'ShareholdingController@getPrintBilling']);
        Route::get('/shareholding/member/{member_id}/payment/month/{pay_date}/pdf/{id}', ['as' => 'service.shareholding.pdf', 'uses' => 'ShareholdingController@getPdfBilling']);
        Route::resource('/shareholding/member/{member_id}/payment', 'ShareholdingController');
        Route::get('/shareholding', ['as' => 'service.shareholding.member', 'uses' => 'ShareholdingController@getMember']);

        // Loan Route...
        Route::post('/loan/showfiles', 'LoanController@postShowFiles');
        Route::post('/loan/uploadfile', 'LoanController@postUploadFile');
        Route::post('/loan/deletefile', 'LoanController@postDeleteFile');
        Route::get('/loan/member/{member_id}/debt', ['as' => 'service.loan.debt', 'uses' => 'LoanController@getDebt']);
        Route::get('/loan/member/{member_id}/debt/print', ['as' => 'service.loan.debtprint', 'uses' => 'LoanController@getDebtPrint']);
        Route::get('/loan/member/{member_id}/debt/pdf', ['as' => 'service.loan.debtpdf', 'uses' => 'LoanController@getDebtPdf']);
        Route::get('/loan/member/{member_id}/loantype/{loantype_id}/createloan', ['as' => 'service.loan.create', 'uses' => 'LoanController@getCreateLoan']);
        Route::get('/loan/member/{member_id}/calsurety', ['as' => 'service.loan.calsurety', 'uses' => 'LoanController@getCalSurety']);
        Route::get('/loan/member/{member_id}/create/normal/employee', ['as' => 'service.loan.create.normal.employee', 'uses' => 'NormalLoanController@getCreateEmployeeLoan']);
        Route::post('/loan/member/{member_id}/create/normal/employee', 'NormalLoanController@postCreateEmployeeLoan');
        Route::get('/loan/member/{member_id}/create/normal/outsider', ['as' => 'service.loan.create.normal.outsider', 'uses' => 'NormalLoanController@getCreateOutsiderLoan']);
        Route::post('/loan/member/{member_id}/create/normal/outsider', 'NormalLoanController@postCreateOutsiderLoan');
        Route::get('/loan/member/{member_id}/create/emerging/employee', ['as' => 'service.loan.create.emerging.employee', 'uses' => 'EmergingLoanController@getCreateEmployeeLoan']);
        Route::post('/loan/member/{member_id}/create/emerging/employee', 'EmergingLoanController@postCreateEmployeeLoan');
        Route::get('/loan/member/{member_id}/create/emerging/outsider', ['as' => 'service.loan.create.emerging.outsider', 'uses' => 'EmergingLoanController@getCreateOutsiderLoan']);
        Route::post('/loan/member/{member_id}/create/emerging/outsider', 'EmergingLoanController@postCreateOutsiderLoan');
        Route::get('/loan/member/{member_id}/create/special/employee', ['as' => 'service.loan.create.special.employee', 'uses' => 'SpecialLoanController@getCreateEmployeeLoan']);
        Route::post('/loan/member/{member_id}/create/special/employee', 'SpecialLoanController@postCreateEmployeeLoan');
        Route::get('/loan/member/{member_id}/create/special/outsider', ['as' => 'service.loan.create.special.outsider', 'uses' => 'SpecialLoanController@getCreateOutsiderLoan']);
        Route::post('/loan/member/{member_id}/create/special/outsider', 'SpecialLoanController@postCreateOutsiderLoan');
        Route::get('/loan/member/{member_id}/detail/{loan_id}/sureties/edit', ['as' => 'service.loan.sureties.edit', 'uses' => 'LoanController@getEditSureties']);
        Route::get('/loan/member/{member_id}/detail/{id}/pmt', ['as' => 'service.loan.pmt', 'uses' => 'LoanController@getPmt']);
        Route::post('/loan/member/{member_id}/detail/{id}/pmt', 'LoanController@postPmt');
        Route::resource('/loan/member/{member_id}/detail', 'LoanController');
        Route::get('/loan', ['as' => 'service.loan.member', 'uses' => 'LoanController@getMember']);

        // Payment Route...
        Route::post('/loan/payment/uploadfile', 'PaymentController@postUploadFile');
        Route::post('/loan/payment/deletefile', 'PaymentController@postDeleteFile');
        Route::get('/loan/payment/{payment_id}/billing/{pay_date}', ['as' => 'service.payment.billing', 'uses' => 'PaymentController@getBilling']);
        Route::get('/loan/payment/{payment_id}/billing/{pay_date}/print', ['as' => 'service.payment.print', 'uses' => 'PaymentController@getPrintBilling']);
        Route::get('/loan/payment/{payment_id}/billing/{pay_date}/pdf', ['as' => 'service.payment.pdf', 'uses' => 'PaymentController@getPdfBilling']);
        Route::get('/loan/{loan_id}/calculate', ['as' => 'service.payment.calculate', 'uses' => 'PaymentController@getCalculate']);
        Route::get('/loan/{loan_id}/close', ['as' => 'service.payment.close', 'uses' => 'PaymentController@getClose']);
        Route::post('/loan/{loan_id}/close', 'PaymentController@postClose');
        Route::get('/loan/{loan_id}/close/print', ['as' => 'service.payment.close.print', 'uses' => 'PaymentController@getPrintClose']);
        Route::post('/loan/{loan_id}/close/print', 'PaymentController@postPrintClose');
        Route::get('/loan/{loan_id}/refinance', ['as' => 'service.payment.refinance', 'uses' => 'PaymentController@getRefinance']);
        Route::post('/loan/{loan_id}/refinance', 'PaymentController@postRefinance');
        Route::get('/loan/{loan_id}/refinance/print', ['as' => 'service.payment.refinance.print', 'uses' => 'PaymentController@getPrintRefinance']);
        Route::post('/loan/{loan_id}/refinance/print', 'PaymentController@postPrintRefinance');
        Route::resource('/loan/{loan_id}/payment', 'PaymentController', ['except' => [ 'index' ]]);

        // Guaruntee Route...
        Route::get('/guaruntee', ['as' => 'service.guaruntee.member', 'uses' => 'GuarunteeController@getMember']);
        Route::get('/guaruntee/member/{member_id}', ['as' => 'service.guaruntee.index', 'uses' => 'GuarunteeController@index']);

        // Dividend Route...
        Route::get('/dividend', ['as' => 'service.dividend.member', 'uses' => 'DividendController@getMember']);
        Route::get('/dividend/member/{member_id}', ['as' => 'service.dividend.member.show', 'uses' => 'DividendController@getMemberDividend']);
        Route::get('/dividend/member/{member_id}/detail/{dividend_id}/edit', ['as' => 'service.dividendmember.edit', 'uses' => 'DividendController@getMemberEdit']);
        Route::post('/dividend/member/{member_id}/detail/{dividend_id}/update', 'DividendController@postMemberUpdate'); 
    });

    // Routine Route...
    Route::group(['prefix' => '/routine'], function() {
        // Routine Shareholding Payment Route...
        Route::get('/shareholding/{routine_id}/detail/create', ['as' => 'routine.shareholding.detail.create', 'uses' => 'RoutineShareholdingController@createDetail']);
        Route::post('/shareholding/{routine_id}/detail', 'RoutineShareholdingController@storeDetail');
        Route::get('/shareholding/{routine_id}/detail/{id}/edit', ['as' => 'routine.shareholding.detail.edit', 'uses' => 'RoutineShareholdingController@editDetail']);
        Route::put('/shareholding/{routine_id}/detail/{id}', 'RoutineShareholdingController@updateDetail');
        Route::delete('/shareholding/{routine_id}/detail/{id}', 'RoutineShareholdingController@deleteDetail');
        Route::post('/shareholding/ajax/calculate', 'RoutineShareholdingController@ajaxcalculate');
        Route::resource('/shareholding', 'RoutineShareholdingController', ['only' => [ 'index', 'show' ]]);

        // Routine Loan Payment Route...
        Route::get('/payment/{routine_id}/detail/create', ['as' => 'routine.payment.detail.create', 'uses' => 'RoutinePaymentController@createDetail']);
        Route::post('/payment/{routine_id}/detail', 'RoutinePaymentController@storeDetail');
        Route::get('/payment/{routine_id}/detail/{id}/edit', ['as' => 'routine.payment.detail.edit', 'uses' => 'RoutinePaymentController@editDetail']);
        Route::put('/payment/{routine_id}/detail/{id}', 'RoutinePaymentController@updateDetail');
        Route::delete('/payment/{routine_id}/detail/{id}', 'RoutinePaymentController@deleteDetail');
        Route::post('/payment/ajax/calculate', 'RoutinePaymentController@ajaxcalculate');
        Route::post('/payment/ajax/payment', 'RoutinePaymentController@ajaxpayment');
        Route::resource('/payment', 'RoutinePaymentController', ['only' => [ 'index', 'show' ]]);

        // Routine Setting Route...
        Route::post('/setting/{id}', 'RoutineSettingController@update');
        Route::resource('/setting', 'RoutineSettingController', ['only' => [ 'index' ]]);
    });

    // Co-op Route...
    Route::group(['prefix' => '/coop'], function() {
        // Loan List Route...
        Route::get('/loanlist', ['as' => 'coop.loan.loanlist', 'uses' => 'LoanController@getLoanList']);

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
        Route::resource('/prefix', 'PrefixController', ['except' => [ 'show' ]]);     
    });

    // Admin Route...
    Route::group(['prefix' => '/admin'], function() {
        // Admin Accounts Route...
        Route::get('/officer/{id}/delete', ['as' => 'admin.officer.delete', 'uses' => 'AdminController@getDelete']);
        Route::get('/officer/inactive', ['as' => 'admin.officer.inactive', 'uses' => 'AdminController@getInactive']);
        Route::post('/officer/{id}/forcedelete', 'AdminController@postForceDelete');
        Route::post('/officer/{id}/restore', 'AdminController@postRestore');
        Route::resource('/officer', 'AdminController');

        //Board Account Route...
        Route::get('/board/{id}/delete', ['as' => 'admin.board.delete', 'uses' => 'BoardController@getDelete']);
        Route::get('/board/inactive', ['as' => 'admin.board.inactive', 'uses' => 'BoardController@getInactive']);
        Route::post('/board/{id}/forcedelete', 'BoardController@postForceDelete');
        Route::post('/board/{id}/restore', 'BoardController@postRestore');
        Route::resource('/board', 'BoardController');

        // User Account Route...
        Route::resource('/account', 'AccountController', ['only' => [ 'index', 'show', 'edit', 'update' ]]);

        // Slotmachine Route...
        Route::get('/reward/{id}/register', ['as' => 'admin.reward.register', 'uses' => 'RewardController@getRegister']);
        Route::get('/reward/{id}/late', ['as' => 'admin.reward.late', 'uses' => 'RewardController@getLate']);
        Route::post('/reward/{id}/register/close', 'RewardController@postCloseRegister');
        Route::post('/reward/register', 'RewardController@postRegister');
        Route::post('/reward/late', 'RewardController@postLate');
        Route::post('/reward/checkmember', 'RewardController@postCheckmember');
        Route::post('/reward/addmember', 'RewardController@postAddmember');
        Route::post('/reward/deletemember', 'RewardController@postDeletemember');
        Route::get('/reward/{id}/slotmachine', ['as' => 'admin.reward.play', 'uses' => 'RewardController@getSlotmachine']);
        Route::post('/reward/winners', 'RewardController@postWinners');
        Route::post('/reward/shuffle', 'RewardController@postShuffle');
        Route::post('/reward/savewinner', 'RewardController@postSavewinner');
        Route::post('/reward/finish', 'RewardController@postFinish');
        Route::resource('/reward', 'RewardController');

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
