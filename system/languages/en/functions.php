<?php

/**
 * Returns months names for current language
 * @return array
 */
function lang_months(){
    return array(
        'January', 'February', 'March', 'April', 'May', 'June', 'July',
        'August', 'September', 'October', 'November', 'December'
    );
}

/**
 * Returns days names for current language
 * @return array
 */
function lang_days(){
    return array(
        'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'
    );
}

function lang_date($date_string){

    $eng_months = array(
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    );

    $date_string = str_replace($eng_months, lang_months(), $date_string);

    return $date_string;

}

/**
 * Converts string from current language to SLUG
 * @return string
 */
function lang_slug($string){

    $string    = trim($string);
    $string    = mb_strtolower($string, 'utf-8');
    $string    = str_replace(' ', '-', $string);

    $slug = preg_replace ('/[^a-zA-Z0-9\-]/u', '-', $string);
    $slug = rtrim($slug, '-');

    while(strstr($slug, '--')){ $slug = str_replace('--', '-', $slug); }

    if (!$slug){ $slug = 'untitled'; }

    return $slug;

}
