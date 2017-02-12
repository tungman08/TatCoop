<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class UploadDocument extends Facade {

    protected static function getFacadeAccessor() { 
        return 'uploaddocument'; 
    }
}