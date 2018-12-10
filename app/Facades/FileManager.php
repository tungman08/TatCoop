<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class FileManager extends Facade {

    protected static function getFacadeAccessor() { 
        return 'filemanager'; 
    }
}