<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class DividendCalculator extends Facade {

    protected static function getFacadeAccessor() { 
        return 'dividendcalculator'; 
    }
}