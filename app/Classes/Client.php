<?php

namespace App\Classes;

use stdClass;

class Client
{
    public function info() {
        $browser = get_browser();
        $info = new stdClass();
        $info->browser_name_regex = $browser->browser_name_regex;
        $info->browser_name_pattern = $browser->browser_name_pattern;
        $info->parent = $browser->parent;
        $info->platform = $browser->platform;
        $info->browser = $browser->browser;
        $info->version = $browser->version;
        $info->ip_address = $this->ip_address();
        $info->session = $this->session();
        return $info;
    }

    protected function ip_address() {
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];
        elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        elseif (isset($_SERVER['HTTP_X_FORWARDED']))
            return $_SERVER['HTTP_X_FORWARDED'];
        elseif (isset($_SERVER['HTTP_FORWARDED_FOR']))
            return $_SERVER['HTTP_FORWARDED_FOR'];
        elseif (isset($_SERVER['HTTP_FORWARDED']))
            return $_SERVER['HTTP_FORWARDED'];
        elseif (isset($_SERVER['REMOTE_ADDR']))
            return $_SERVER['REMOTE_ADDR'];
        else
            return 'Unknown';
    }

    protected function session() {
        $session = session_id();

        if(empty($session))
            session_start();

        return session_id();
    }
}
