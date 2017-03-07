<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class ClientInfo extends Facade {

    protected static function getFacadeAccessor() { 
        return 'clientinfo'; 
    }
}