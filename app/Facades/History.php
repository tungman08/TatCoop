<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class History extends Facade {

    protected static function getFacadeAccessor() { 
        return 'history'; 
    }
}