<?php

/************************
 * Conversion Functions *
 ************************/


/**
 * Convert decimal hours (industrial minutes) to sexagesimal hours:minutes.
 * @param string $input
 * @return string
 */
function hourDec2hurSex($input){
    $hour = array();
    if(!preg_match('/([0-9]*).([0-9]{1,2})([0-9]?)/', $input, $hour)){
        return "Error: Input format needs to be like '0.0'.";
    }
    $h = $hour[1];
    $r = ($hour[3] > 4) ? 1 : 0;
    $m = strlen($hour[2]) == 1 ? $hour[2]*10 : $hour[2];
    $m = (int) round(($m+$r)/100*60);
    $hm = (int) ($m/60);
    $h += $hm;
    $m -= $hm*60;
    $h = str_pad($h, 2, '0', STR_PAD_LEFT);
    $m = str_pad($m, 2, '0', STR_PAD_LEFT);
    return $h.':'.$m;
}

/**
 * Convert sexagesimal hours:minutes to decimal hours (industrial minutes).
 * @param string $input
 * @return string
 */
function hourSex2hourDec($input){
    $hour = array();
    if(!preg_match('/([0-9]{2,}):([0-9]{2})/', $input, $hour)){
         return "Error: Input format needs to be like '00:00'.";
    }
    return number_format($hour[1]+(round($hour[2]/60, 2)), 2, '.', '');
}
