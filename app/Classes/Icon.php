<?php

namespace App\Classes;

class Icon
{
    public static function platform($value) {
        switch (strtolower($value)) {
            default:
                $icon = 'circle-thin';
                break;
            case 'android':
                $icon = 'android';
                break;
            case 'linux':
                $icon = 'linux';
                break;
            case 'ios':
            case 'macosx':
                $icon = 'apple';
                break;
            case 'win32':
            case 'winvista':
            case 'win7':
            case 'win8':
            case 'win8.1':
            case 'win10':
                $icon = 'windows';
                break;
        }

        return "<i class=\"fa fa-fw fa-$icon\"></i> $value";
    }

    public static function browser($value) {
        switch (strtolower($value)) {
            default:
                $icon = 'circle-thin';
                break;
            case 'chrome':
                $icon = 'chrome';
                break;
            case 'edge':
                $icon = 'edge';
                break;
            case 'firefox':
                $icon = 'firefox';
                break;
            case 'ie':
                $icon = 'internet-explorer';
                break;
            case 'opera':
                $icon = 'opera';
                break;
            case 'safari':
                $icon = 'safari';
                break;
        }

        return "<i class=\"fa fa-fw fa-$icon\"></i> $value";
    }

    public static function user($value) {
        return "<i class=\"fa fa-fw fa-user\"></i> $value";
    }
}