<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class MainMenu extends Facade {

    protected static function getFacadeAccessor() { 
        return 'mainmenu'; 
    }
}