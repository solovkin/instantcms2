<?php

/**
 * Returns months names for current language
 * @return array
 */
function lang_months(){
    return array(
        'января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
        'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'
    );
}

/**
 * Returns days names for current language
 * @return array
 */
function lang_days(){
    return array(
        'вс', 'пн', 'вт', 'ср', 'чт', 'пт', 'сб'
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

    $slug = preg_replace ('/[^a-zA-Zа-яА-Я0-9\-]/u', '-', $string);
    $slug = rtrim($slug, '-');

    while(strstr($slug, '--')){ $slug = str_replace('--', '-', $slug); }

    $ru_en = array(
                    'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d',
                    'е'=>'e','ё'=>'yo','ж'=>'zh','з'=>'z',
                    'и'=>'i','й'=>'i','к'=>'k','л'=>'l','м'=>'m',
                    'н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s',
                    'т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c',
                    'ч'=>'ch','ш'=>'sh','щ'=>'sch','ъ'=>'','ы'=>'y',
                    'ь'=>'','э'=>'e','ю'=>'yu','я'=>'ja'
                    );

    foreach($ru_en as $ru=>$en){
        $slug = str_replace($ru, $en, $slug);
    }

    if (!$slug){ $slug = 'untitled'; }

    return $slug;

}
