<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App;
use Auth;
use Blade;
use Validator;
use App\Theme;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /* bind data to website views */
        view()->composer('website.member.*', function ($view) {
            if (Auth::check()) {
                $view->with('user', Auth::user())
                    ->with('skins', Theme::All());
            }
        });

        /* bind data to admin views */
        view()->composer('admin.*', function ($view) {
            if (Auth::guard('admins')->check()) {
                $admin = Auth::guard('admins')->user();

                $view->with('admin', $admin)
                    ->with('is_super', $admin->role_id == 1)
                    ->with('is_admin', $admin->role_id == 2)
                    ->with('is_viewer', $admin->role_id == 3);
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('clientinfo', function() {
            return new \App\Classes\ClientInfo;
        });

        App::bind('diamond', function() {
            return new \App\Classes\Diamond;
        });

        App::bind('icon', function() {
            return new \App\Classes\Icon;
        });
 
        App::bind('mainmenu', function() {
            return new \App\Classes\MainMenu;
        });
               
        App::bind('statistic', function() {
            return new \App\Classes\Statistic;
        });

        App::bind('memberproperty', function() {
            return new \App\Classes\MemberProperty;
        });

        App::bind('number', function() {
            return new \App\Classes\Number;
        }); 

        App::bind('uploaddocument', function() {
            return new \App\Classes\UploadDocument;
        });
        
        App::bind('history', function() {
            return new \App\Classes\History;
        });  

        App::bind('bing', function() {
            return new \App\Classes\Bing;
        });   

        App::bind('loancalculator', function() {
            return new \App\Classes\LoanCalculator;
        }); 
        
        App::bind('loanmanager', function() {
            return new \App\Classes\LoanManager;
        });    

        App::bind('dividendcalculator', function() {
            return new \App\Classes\DividendCalculator;
        });    

        App::bind('shareholdingcalculator', function() {
            return new \App\Classes\ShareholdingCalculator;
        });  

        App::bind('dashboard', function() {
            return new \App\Classes\Dashboard;
        });  
        
        App::bind('filemanager', function() {
            return new \App\Classes\FileManager;
        }); 
    }
}
