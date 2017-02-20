<?php

namespace App\Classes;

use Carbon\Carbon;

class Diamond extends Carbon
{
    public function thai_format($format) {
        $thaidate = ['Sun' => ['l' => 'อาทิตย์', 'D' => 'อา.'],
            'Mon' => ['l' => 'จันทร์', 'D' => 'จ.'],
            'Tue' => ['l' => 'อังคาร', 'D' => 'อ.'],
            'Wed' => ['l' => 'พุธ', 'D' => 'พ.'],
            'Thu' => ['l' => 'พฤหัสบดี', 'D' => 'พฤ.'],
            'Fri' => ['l' => 'ศุกร์', 'D' => 'ศ.'],
            'Sat' => ['l' => 'เสาร์', 'D' => 'ส.'],
            'Jan' => ['F' => 'มกราคม', 'M' => 'ม.ค.'],
            'Feb' => ['F' => 'กุมภาพันธ์', 'M' => 'ก.พ.'],
            'Mar' => ['F' => 'มีนาคม', 'M' => 'มี.ค.'],
            'Apr' => ['F' => 'เมษายน', 'M' => 'เม.ย.'],
            'May' => ['F' => 'พฤษภาคม', 'M' => 'พ.ค.'],
            'Jun' => ['F' => 'มิถุนายน', 'M' => 'มิ.ย.'],
            'Jul' => ['F' => 'กรกฎาคม', 'M' => 'ก.ค.'],
            'Aug' => ['F' => 'สิงหาคม', 'M' => 'ส.ค.'],
            'Sep' => ['F' => 'กันยายน', 'M' => 'ก.ย.'],
            'Oct' => ['F' => 'ตุลาคม', 'M' => 'ต.ค.'],
            'Nov' => ['F' => 'พฤศจิกายน', 'M' => 'พ.ย.'],
            'Dec' => ['F' => 'ธันวาคม', 'M' => 'ธ.ค.']];

        $chrarray = str_split($format);
        $keys = 'roYyFMlD';
        $previous = '';
        $newformat = [];

        foreach ($chrarray as $chr) {
            $match = strpos($keys, $chr);

            if ($match !== FALSE && $previous !== '\\') {
                $default = $this->format($chr);

                switch ($chr) {
                    case 'r':
                        $year = strval(intval($this->format('Y')) + 543);
                        $thai_format = "{$thaidate[$this->format('D')]['D']} d {$thaidate[$this->format('M')]['M']} $year H:i:s O";
                        $newformat[] = $thai_format;
                        break;
                    case 'o':
                    case 'Y':
                        $thai_format = strval(intval($default) + 543);
                        $newformat[] = $thai_format;
                        break;
                    case 'y':
                        $thai_format = substr(strval(intval($default) + 543), -2);
                        $newformat[] = $thai_format;
                        break;
                    default:
                        $thai_format = $thaidate[substr($default, 0, 3)][$chr];
                        $newformat[] = $thai_format;
                        break;
                }

                $previous = $chr;
            }
            else {
                $newformat[] = $chr;
                $previous = $chr;
            }
        }

        return $this->format(implode($newformat));
    }

    public function thai_diffForHumans($other = null, $absolute = false, $short = false) {
        parent::setLocale('th');

        return $this->diffForHumans($other, $absolute, $short);
    }
}
