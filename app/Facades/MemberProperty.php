<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class MemberProperty extends Facade {

    protected static function getFacadeAccessor() { 
        return 'memberproperty'; 
    }
}