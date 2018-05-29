<?php


namespace App\Helpers;

use Carbon\Carbon;

class DateHelper {

    public static function dateFormat1($date) {
        if ($date) {
            $dt = new DateTime($date);
            return $dt->format("m/d/y"); // 10/27/2014
        }
    }

    public static function dateStringToCarbon($date, $format = 'm/d/Y')
    {
        if(!$date instanceof Carbon) {
            $validDate = false;
            try {
                $date = Carbon::createFromFormat($format, $date);
                $validDate = true;
            } catch(Exception $e) { }

            if(!$validDate) {
                try {
                    $date = Carbon::parse($date);
                    $validDate = true;
                } catch(Exception $e) { }
            }

            if(!$validDate) {
                $date = NULL;
            }
        }
        return $date;
    }

    // A helper function to determine if date input is valid or not
    function validateDate($date, $format = 'Y-m-d H:i:s')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

}