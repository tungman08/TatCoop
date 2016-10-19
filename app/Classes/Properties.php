<?php

namespace App\Classes;

class Properties
{
    // magic method
    public function __get($name) {
        $method = 'get' . $name;

        return $this->$method();
    }

    public function __set($name, $value) {
        $method = 'set' . $name;

        $this->$method($value);
    }
}