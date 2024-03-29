<?php

class fieldCaption extends cmsFormField {

    public $title           = LANG_PARSER_CAPTION;
    public $is_public       = false;
    public $sql             = 'varchar(255) NULL DEFAULT NULL';
    public $filter_type     = 'str';

    public function getOptions(){

        return array(
            new fieldNumber('min_length', array(
                'title' => LANG_PARSER_TEXT_MIN_LEN,
                'default' => 0
            )),
            new fieldNumber('max_length', array(
                'title' => LANG_PARSER_TEXT_MAX_LEN,
                'default' => 255
            )),
        );

    }

    public function getRules() {

        if ($this->getOption('min_length')){
            $this->rules[] = array('min_length', $this->getOption('min_length'));
        }

        if ($this->getOption('max_length')){
            $this->rules[] = array('max_length', $this->getOption('max_length'));
        }

        return $this->rules;

    }

    public function parse($value){
        return '<h1>'.htmlspecialchars($value).'</h1>';
    }

    public function applyFilter($model, $value) {
        return $model->filterLike($this->name, "%{$value}%");
    }

}
