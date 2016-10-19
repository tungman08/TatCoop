<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class BingPhoto extends Facade {

    protected static function getFacadeAccessor() { 
        return 'bingphoto'; 
    }
}