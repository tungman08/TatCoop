<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Routine extends Facade {

    protected static function getFacadeAccessor() { 
        return 'routine'; 
    }
}