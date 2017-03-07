<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Loan extends Facade {

    protected static function getFacadeAccessor() { 
        return 'loan'; 
    }
}