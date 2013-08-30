<?php

/**
 * Выводит строку безопасную для html
 * @param type $string
 */
function html($string){
    echo htmlspecialchars($string);
}

/**
 * Очищает строку от тегов и обрезает до нужной длины
 * @param string $string
 * @param int $max_length
 * @return string
 */
function html_clean($string, $max_length=false){

    $string = strip_tags($string);

    if (is_int($max_length)){
        $length = mb_strlen($string);
        $string = mb_substr($string, 0, $max_length);
        if ($length > $max_length) { $string .= '...'; }
    }

    return $string;

}

/**
 * Возвращает панель со страницами
 *
 * @param int $page Текущая страница
 * @param int $perpage Записей на одной странице
 * @param int $total Количество записей
 * @param str $base_uri Базовый URL, может быть массивом из элементов first и base
 */
function html_pagebar($page, $perpage, $total, $base_uri=false, $query=array()){

	if (!$total){ return; }

    $pages = ceil($total / $perpage);
    if($pages<=1) { return; }

    $core = cmsCore::getInstance();

    $anchor = '';

    if (is_string($base_uri) && mb_strstr($base_uri, '#')){
        list($base_uri, $anchor) = explode('#', $base_uri);
    }

    if ($anchor) { $anchor = '#' . $anchor; }

    if (!$base_uri) { $base_uri = $core->uri_absolute; }

    if (!is_array($base_uri)){
        $base_uri = array(
            'first'=>$base_uri,
            'base'=>$base_uri
        );
    }

    $html   = '';

    $html .= '<div class="pagebar">';
    $html .= '<span class="pagebar_title">'.LANG_PAGES.': </span>';

    if ($page > 1){
        $query['page'] = ($page-1);
        $uri = ($query['page']==1 ? $base_uri['first'] : $base_uri['base']);
        $sep = mb_strstr($uri, '?') ? '&' : '?';
        if ($query['page'] == 1) { unset($query['page']); }
        $html .= ' <a href="'. $uri . ($query ? $sep .http_build_query($query) : '') . $anchor . '" class="pagebar_page">'.LANG_PAGE_PREV.'</a> ';
    }

    for ($p=1; $p<=$pages; $p++){
        if ($p != $page) {
            $query['page'] = $p;
            $uri = ($query['page']==1 ? $base_uri['first'] : $base_uri['base']);
            $sep = mb_strstr($uri, '?') ? '&' : '?';
            if ($query['page'] == 1) { unset($query['page']); }
            $html .= ' <a href="'. $uri . ($query ? $sep.http_build_query($query) : '') . $anchor . '" class="pagebar_page">'.$p.'</a> ';
        } else {
            $html .= '<span class="pagebar_current">'.$p.'</span>';
        }
    }

    if ($page < $pages){
        $query['page'] = ($page+1);
        $uri = ($query['page']==1 ? $base_uri['first'] : $base_uri['base']);
        $sep = mb_strstr($uri, '?') ? '&' : '?';
        if ($query['page'] == 1) { unset($query['page']); }
        $html .= ' <a href="'. $uri . ($query ? $sep.http_build_query($query) : '') . $anchor . '" class="pagebar_page">'.LANG_PAGE_NEXT.'</a> ';
    }

    $from   = $page * $perpage - $perpage + 1;
    $to     = $page * $perpage; if ($to>$total) { $to = $total; }

    $html  .= '<div class="pagebar_notice">'.sprintf(LANG_PAGES_SHOWN, $from, $to, $total).'</div>';

    $html .= '</div>';

	return $html;

}

/**
 * Возвращает ссылку на указанное действие контроллера
 * с добавлением пути от корня сайта
 * @param string $controller
 * @param string $action
 * @param array|str|int $params Параметры, массив
 * @return string
 */
function href_to($controller, $action='', $params=false){

    $config = cmsConfig::getInstance();

	$href = $config->root . href_to_rel($controller, $action, $params);

	return $href;

}

/**
 * Возвращает ссылку на указанное действие контроллера
 * с добавлением хоста сайта
 * @param string $controller
 * @param string $action
 * @param array|str|int $params Параметры, массив
 * @return string
 */
function href_to_abs($controller, $action='', $params=false){

    $config = cmsConfig::getInstance();

	$href = $config->host . '/' . href_to_rel($controller, $action, $params);

	return $href;

}

/**
 * Возвращает ссылку на указанное действие контроллера без добавления корня URL
 *
 * @param string $controller
 * @param string $action
 * @param array|str|int $params Параметры, массив
 * @return string
 */
function href_to_rel($controller, $action='', $params=false){

    $controller = trim($controller, '/ ');

    $controller_alias = cmsCore::getControllerAliasByName($controller);
    if ($controller_alias) { $controller = $controller_alias; }

	$href = $controller;

	if($action){ $href .= '/' . $action; }
	if($params){
        if (is_array($params)){
            $href .= '/' . implode("/", $params);
        } else {
            $href .= '/' . $params;
        }
    }

    $href = rtrim($href, '/');

	return  $href;

}

/**
 * Возвращает ссылку на главную страницу сайта
 * @return string
 */
function href_to_home(){
    $config = cmsConfig::getInstance();
	$href = $config->host . '/';
    return $href;
}

/**
 * Возвращает отформатированную строку аттрибутов тега
 * @param array $attributes
 * @return string
 */
function html_attr_str($attributes){
    $attr_str = '';
    unset($attributes['class']);
    if (sizeof($attributes)){
        foreach($attributes as $key=>$val){
            $attr_str .= "{$key}=\"{$val}\" ";
        }
    }
    return $attr_str;
}

/**
 * Возвращает тег <input>
 *
 * @param string $type Тип поля
 * @param string $name Имя поля
 * @param string $value Значение по-умолчанию
 * @return html
 */
function html_input($type='text', $name='', $value='', $attributes=array()){
    if ($type=='password'){ $attributes['autocomplete'] = 'off'; }
    $attr_str = html_attr_str($attributes);
    $class = 'input';
    if (isset($attributes['class'])) { $class .= ' '.$attributes['class']; }
	return '<input type="'.$type.'" class="'.$class.'" name="'.$name.'" value="'.htmlspecialchars($value).'" '.$attr_str.'/>';
}

function html_file_input($name, $attributes=array()){
    $attr_str = html_attr_str($attributes);
    $class = 'file-input';
    if (isset($attributes['class'])) { $class .= ' '.$attributes['class']; }
	return '<input type="file" class="'.$class.'" name="'.$name.'" '.$attr_str.'/>';
}

function html_textarea($name='', $value='', $attributes=array()){
    $attr_str = html_attr_str($attributes);
    $class = 'textarea';
    if (isset($attributes['class'])) { $class .= ' '.$attributes['class']; }
	$html = '<textarea name="'.$name.'" class="'.$class.'" '.$attr_str.'>'.htmlspecialchars($value).'</textarea>';
	return $html;
}

function html_back_button(){
	return '<div class="back_button"><a href="javascript:window.history.go(-1);">'.LANG_BACK_BUTTON.'</a></div>';
}

function html_checkbox($name, $checked=false, $value=1, $attributes=array()){
    if ($checked) { $attributes['checked'] = 'checked'; }
    $attr_str = html_attr_str($attributes);
    $class = 'input-checkbox';
    if (isset($attributes['class'])) { $class .= ' '.$attributes['class']; }
	return '<input type="checkbox" class="'.$class.'" name="'.$name.'" value="'.$value.'" '.$attr_str.'/>';
}

function html_radio($name, $checked=false, $value=1, $attributes=array()){
    if ($checked) { $attributes['checked'] = 'checked'; }
    $attr_str = html_attr_str($attributes);
	return '<input type="radio" class="input_radio" name="'.$name.'" value="'.$value.'" '.$attr_str.'/>';
}

function html_date($date=false, $is_time=false){
    $config = cmsConfig::getInstance();
    $timestamp = $date ? strtotime($date) : time();
    $date = date($config->date_format, $timestamp);
    if ($is_time){ $date .= ' ' . date('H:i', $timestamp); }
    return htmlspecialchars($date);
}

function html_time($date){
    $timestamp = strtotime($date);
    return date('H:i', $timestamp);
}

function html_date_time($date=false){
    return html_date($date, true);
}

function html_datepicker($name='', $value='', $attributes=array()){
    $config = cmsConfig::getInstance();
    if (isset($attributes['id'])){
        $id = $attributes['id'];
        unset($attributes['id']);
    } else {
        $id = $name;
    }
    $attr_str = html_attr_str($attributes);
	$html  = '<input type="text" name="'.$name.'" value="'.htmlspecialchars($value).'" class="date-input"  id="'.$id.'" '.$attr_str.'/>';
    $html .= '<script type="text/javascript">';
    $html .= "$('#{$id}').datepicker({showStatus: true, showOn: 'both', dateFormat:'{$config->date_format_js}'});";
    $html .= '</script>';
    return $html;
}

/**
 * Возвращает кнопку "Отправить" <input type="submit">
 *
 * @param string $caption
 * @return html
 */
function html_submit($caption=LANG_SUBMIT, $name='submit', $attributes=array()){
    $attr_str = html_attr_str($attributes);
    $class = 'button-submit';
    if (isset($attributes['class'])) { $class .= ' '.$attributes['class']; }
	return '<input class="'.$class.'" type="submit" name="'.$name.'" value="'.htmlspecialchars($caption).'" '.$attr_str.'/>';
}

/**
 * Возвращает html-код кнопки
 *
 * @param str $caption Заголовок
 * @param str $name Название кнопки
 * @param str $onclick Содержимое аттрибута onclick (javascript)
 * @return html
 */
function html_button($caption, $name, $onclick='', $attributes=array()){
    $attr_str = html_attr_str($attributes);
    $class = 'button';
    if (isset($attributes['class'])) { $class .= ' '.$attributes['class']; }
	return '<input type="button" class="'.$class.'" name="'.$name.'" value="'.htmlspecialchars($caption).'" onclick="'.$onclick.'" '.$attr_str.'/>';
}

function html_avatar_image($avatars, $size_preset='small'){

    $config = cmsConfig::getInstance();

    $default = array(
        'normal' => 'default/avatar.jpg',
        'small' => 'default/avatar_small.jpg',
        'micro' => 'default/avatar_micro.png'
    );

    if (empty($avatars)){
		$avatars = $default;
    }

    if (!is_array($avatars)){
        $avatars = cmsModel::yamlToArray($avatars);
    }

    $src = $avatars[ $size_preset ];

	if (!strstr($src, $config->upload_host)){
        $src = $config->upload_host . '/' . $src;
    }

    return '<img src="'.$src.'" border="0" />';

}

function html_image($image, $size_preset='small', $alt=''){

    $config = cmsConfig::getInstance();

    if (!is_array($image)){
        $image = cmsModel::yamlToArray($image);
    }

    if (!$image){
        return false;
    }

    $keys = array_keys($image);
    if ($keys[0]===0) { $image = $image[0]; }

    $src = $image[ $size_preset ];

    if (!strstr($src, $config->upload_host)){
        $src = $config->upload_host . '/' . $src;
    }

    return '<img src="'.$src.'" border="0" alt="'.htmlspecialchars($alt).'" />';

}

function html_wysiwyg($field_id, $content='', $wysiwyg=false){

    $config = cmsConfig::getInstance();

    if (!$wysiwyg){
        $config = cmsConfig::getInstance();
        $wysiwyg = $config->wysiwyg;
    }

	$connector = 'wysiwyg/' . $wysiwyg . '/wysiwyg.class.php';

	if (!file_exists($config->root_path . $connector)){
		return '<textarea id="'.$field_id.'" name="'.$field_id.'">'.$content.'</textarea>';
	}

    cmsCore::includeFile($connector);

    $editor = new cmsWysiwyg();

    ob_start(); $editor->displayEditor($field_id, $content);

    return ob_get_clean();

}

function html_editor($field_id, $content='', $options=array()){

    $markitup_controller = cmsCore::getController('markitup', new cmsRequest(array(), cmsRequest::CTX_INTERNAL));

    return $markitup_controller->getEditorWidget($field_id, $content, $options);

}

/**
 * Генерирует список опций
 *
 * @param string $name Имя списка
 * @param array $items Массив элементов списка (значение => заголовок)
 * @param string $selected Значение выбранного элемента
 * @param array $attributes Массив аттрибутов тега
 * @return html
 */
function html_select($name, $items, $selected='', $attributes=array()){
    $attr_str = html_attr_str($attributes);
	$html = '<select name="'.$name.'" '.$attr_str.'>'."\n";
	foreach ($items as $value=>$title){
		if ($selected == $value) { $sel = 'selected'; } else { $sel = ''; }
		$html .= "\t" . '<option value="'.htmlspecialchars($value).'" '.$sel.'>'.htmlspecialchars($title).'</option>' . "\n";
	}
	$html .= '</select>'."\n";
	return $html;
}

/**
 * Генерирует список опций с множественным выбором
 *
 * @param string $name Имя списка
 * @param array $items Массив элементов списка (значение => заголовок)
 * @param string $selected Массив значений выбранных элементов
 * @param array $attributes Массив аттрибутов тега
 * @return html
 */
function html_select_multiple($name, $items, $selected=array(), $attributes=array()){
    $attr_str = html_attr_str($attributes);
	$html = '<div class="input_checkbox_list" '.$attr_str.'>'."\n";
	foreach ($items as $value=>$title){
		$checked = is_array($selected) && in_array($value, $selected);
		$html .= "\t" . '<label>' .
                 html_checkbox($name.'[]', $checked, $value) . ' ' .
                 htmlspecialchars($title) . '</label>' . "\n";

	}
	$html .= '</div>'."\n";
	return $html;
}

/**
 * Генерирует и возвращает дерево категорий в виде комбо-бокса
 * @param array $tree Массив с элементами дерева NS
 * @param int $selected_id ID выбранного элемента
 * @return html
 */
function html_category_list($tree, $selected_id=0){
	$html = '<select name="category_id" id="category_id" class="combobox">'."\n";
	foreach ($tree as $cat){
		$padding = str_repeat('---', $cat['ns_level']).' ';
		if ($selected_id == $cat['id']) { $selected = 'selected'; } else { $selected = ''; }
		$html .= "\t" . '<option value="'.$cat['id'].'" '.$selected.'>'.$padding.' '.htmlspecialchars($cat['title']).'</option>' . "\n";
	}
    $html .= '</select>'."\n";
	return $html;
}

/**
 * Генерирует две радио-кнопки ВКЛ и ВЫКЛ
 *
 * @param string $name
 * @param bool $active
 * @return html
 */
function html_switch($name, $active){
	$html = '';
	$html .= '<label><input type="radio" name="'.$name.'" value="1" '. ($active ? 'checked' : '') .'/> ' . LANG_ON . "</label> \n";
	$html .= '<label><input type="radio" name="'.$name.'" value="0" '. (!$active ? 'checked' : '') .'/> ' . LANG_OFF . "</label> \n";
	return $html;
}

/**
 * Возвращает строку содержащую число со знаком плюс или минус
 * @param int $number
 * @return string
 */
function html_signed_num($number){
    if ($number > 0){
        return "+{$number}";
    } else {
        return "{$number}";
    }
}

function html_bool_span($value, $condition){
    if ($condition){
        return '<span class="positive">' . $value . '</span>';
    } else {
        return '<span class="negative">' . $value . '</span>';
    }
}

/**
 * Возвращает строку "positive" для положительных чисел,
 * "negative" для отрицательных и "zero" для ноля
 * @param int $number
 * @return string
 */
function html_signed_class($number){
    if ($number > 0){
        return "positive";
    } else if ($number < 0){
        return "negative";
    } else {
        return "zero";
    }
}

/**
 * Возвращает скрытое поле, содержащее актуальный CSRF-токен
 * @param mixed $seed
 * @return string
 */
function html_csrf_token($seed=false){
    $token = cmsForm::generateCSRFToken($seed);
    return html_input('hidden', 'csrf_token', $token);
}

/**
 * Возвращает число с числительным в нужном склонении
 * @param int $num
 * @param string $one
 * @param string $two
 * @param string $many
 * @return string
 */
function html_spellcount($num, $one, $two=false, $many=false) {

    if (!$two && !$many){
        list($one, $two, $many) = explode('|', $one);
    }

    if ($num%10==1 && $num%100!=11){
        return $num.' '.$one;
    }
    elseif($num%10>=2 && $num%10<=4 && ($num%100<10 || $num%100>=20)){
        return $num.' '.$two;
    }
    else{
        return $num.' '.$many;
    }
    return $num.' '.$one;

}

/**
 * Возвращает отформатированный размер файла с единицей измерения
 * @param int $bytes
 * @param bool $round
 * @return string
 */
function html_file_size($bytes, $round=false){

    if(empty($bytes)) { return 0; }

    $s = array(LANG_B, LANG_KB, LANG_MB, LANG_GB, LANG_TB, LANG_PB);
    $e = floor(log($bytes)/log(1024));

    $pattern = $round ? '%d' : '%.2f';

    $output = sprintf($pattern.' '.$s[$e], ($bytes/pow(1024, floor($e))));

    return $output;

}

/**
 * Возвращает склеенный в одну строку массив строк
 * @param array $array
 * @return string
 */
function html_each($array){

    $result = '';

    if (is_array($array)){
        $result = implode('', $array);
    }

    return $result;

}

/**
 * Строит рекурсивно список UL из массива
 *
 * @author acmol
 *
 * @param array $array
 * @return string
 */
function html_array_to_list($array){

    $html = '<ul>' . "\n";

    foreach($array as $key => $elem){

        if(!is_array($elem)){
            $html = $html . '<li>'.$elem.'</li>' . "\n";
        }
        else {
            $html = $html . '<li class="folder">'.$key.' '.html_array_to_list($elem).'</li>' . "\n";
        }

    }

    $html = $html . "</ul>" . "\n";

    return $html;

}

function html_tags_bar($tags){

    if (!$tags) { return ''; }

    if (!is_array($tags)){
        $tags = explode(',', $tags);
    }

    foreach($tags as $id=>$tag){
        $tag = trim($tag);
        $tags[$id] = '<a href="'.href_to('tags', 'search').'?q='.urlencode($tag).'">'.$tag.'</a>';
    }

    $tags_bar = implode(', ', $tags);

    return $tags_bar;

}

/**
 * Вырезает из HTML-кода пробелы, табуляции и переносы строк
 * @param string $html
 * @return string
 */
function html_minify($html){

    $search = array(
        '/\>[^\S ]+/s',
        '/[^\S ]+\</s',
        '/(\s)+/s'
    );

    $replace = array(
        '>',
        '<',
        '\\1'
    );

    $html = preg_replace($search, $replace, $html);

    return $html;

}
