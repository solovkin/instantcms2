<?php

class fieldUrl extends cmsFormField {

    public $title = LANG_PARSER_URL;
    public $sql   = 'text';
    public $filter_type = 'str';

    public function getOptions(){
        return array(
            new fieldCheckbox('redirect', array(
                'title' => LANG_PARSER_URL_REDIRECT,
                'default' => false
            )),
            new fieldCheckbox('auto_http', array(
                'title' => LANG_PARSER_URL_AUTO_HTTP,
                'default' => true
            )),
        );
    }
    
    public function parse($value){

        $href = $value;

        if ($this->getOption('auto_http')){
            if (!strstr($href, 'http://')) { $href = 'http://' . $href; }
        }

        if ($this->getOption('redirect')){
            $config = cmsConfig::getInstance();
            $href = $config->root . 'redirect?url=' . $href;
        }

        return '<a href="'.htmlspecialchars($href).'">'.$value.'</a>';

    }

    public function applyFilter($model, $value) {
        return $model->filterLike($this->name, "%{$value}%");
    }

}
