<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ShareholdingCalculator extends Facade {

    protected static function getFacadeAccessor() { 
        return 'shareholdingcalculator'; 
    }
}