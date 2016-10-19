<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Statistic extends Facade {

    protected static function getFacadeAccessor() { 
        return 'statistic'; 
    }
}