<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Bing extends Facade {

    protected static function getFacadeAccessor() { 
        return 'bing'; 
    }
}