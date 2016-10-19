<?php

namespace App\Classes;

class Number
{
    public static function toText($number) {
        if (!is_numeric($number)) {
            return 'Cann\'t convert.';
        }

        $num = explode('.', strval($number));
        $numtext = self::numtotext($num[0]);
        $dectext = (count($num) > 1) ? self::dectotext($num[1]) : '';

        return (count($num) == 1) ? $numtext : "{$numtext}จุด{$dectext}";
    }

    public static function toBaht($number) {
        if (!is_numeric($number)) {
            return 'Cann\'t convert.';
        }

        $num = explode('.', strval($number));
        $numtext = self::numtotext($num[0]);
        $dectext = (count($num) > 1) ? (strlen($num[1]) <= 2) ? 'บาท' . 
            self::numtotext((strlen($num[1]) == 1) ? $num[1] . '0' : $num[1]) . 'สตางค์' : 'จุด' . 
            self::dectotext($num[1]) . 'บาท' : '';

        return (count($num) > 1) ? "{$numtext}{$dectext}" : "{$numtext}บาทถ้วน";
    }

    private static function numtotext($string) {
        $num = self::splitstr($string, 6);

        $loop = count($num);
        $thai = array();

        for ($i = 0; $i < $loop; $i++) {
            $numthai = self::numtothai($num[$i]);

            for ($m = 0; $m < $i; $m++) {
                $numthai .= 'ล้าน';
            }

            array_push($thai, $numthai);
        }

        return implode(array_reverse($thai));
    }

    private static function numtothai($string) {
        $len = strlen($string);
        $chrarray = array_reverse(str_split($string));
        $pos = array('', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน');
        $num = array('ศูนย์', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า');
        $thai = array();

        for ($i = 0; $i < $len; $i++) {
            if ($chrarray[$i] != '0') {
                if ($chrarray[$i] == '1' && $i == 0) {
                     array_push($thai, ($len > 1) ? 'เอ็ด' : 'หนึ่ง' . $pos[$i]);
                }
                else if ($chrarray[$i] == '1' && $i == 1) {
                    array_push($thai, $pos[$i]);
                }
                else if ($chrarray[$i] == '2' && $i == 1) {
                    array_push($thai, 'ยี่' . $pos[$i]);
                }
                else {
                    array_push($thai, $num[intval($chrarray[$i])] . $pos[$i]);
                }
            }
        }

        return implode(array_reverse($thai));
    }

    private static function dectotext($string) {
        $chrarray = str_split($string);
        $num = array('ศูนย์', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า');
        $thai = array();

        foreach ($chrarray as $chr) {
            array_push($thai, (is_numeric($chr)) ? $num[intval($chr)] : $chr);
        }

        return implode($thai);
    }

    private static function splitstr($string, $length) {
        $len = strlen($string);
        $floor = floor($len / $length);
        $mod = $len % $length;
        $result = array();

        if ($floor > 0) {
            for($i = 0; $i < $floor; $i++) {
                array_push($result, substr($string, 0 - $length * ($i + 1), $length));
            }
        }

        if ($mod > 0) {
            array_push($result, substr($string, 0, $mod));
        }

        return $result;
    }
}