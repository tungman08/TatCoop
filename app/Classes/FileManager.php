<?php

namespace App\Classes;

class FileManager
{
    public static function get($directory, $filename) {
        return 'https://www.' . env('APP_DOMAIN') . '/storage/file/' . $directory . '/' . $filename;
    }
}