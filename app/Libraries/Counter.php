<?php

namespace App\Libraries;

class Counter
{
    public static function display($statistic) {
        $counter = str_split(str_pad(strval($statistic), 9, '0', STR_PAD_LEFT));
        $result = '';

        foreach ($counter as $c) {
            $result .= '<li><img class="' . self::toClass($c) . '" src="' . asset('images/blank.png') . '" /></li>';
        }

        return $result;
    }

    protected static function toClass($chr) {
        switch($chr) {
            default: return 'zero'; break;
            case '1': return 'one'; break;
            case '2': return 'two'; break;
            case '3': return 'three'; break;
            case '4': return 'four'; break;
            case '5': return 'five'; break;
            case '6': return 'six'; break;
            case '7': return 'seven'; break;
            case '8': return 'eight'; break;
            case '9': return 'nine'; break;
        }
    }
}