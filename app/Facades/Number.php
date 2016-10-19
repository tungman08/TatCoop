<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Number extends Facade {

    protected static function getFacadeAccessor() { 
        return 'number'; 
    }
}