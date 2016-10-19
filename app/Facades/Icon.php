<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Diamond extends Facade {

    protected static function getFacadeAccessor() { 
        return 'icon'; 
    }
}