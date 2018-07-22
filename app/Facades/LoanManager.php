<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class LoanManager extends Facade {

    protected static function getFacadeAccessor() { 
        return 'loanmanager'; 
    }
}