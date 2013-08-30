<?php

class fieldUser extends cmsFormField {

    public $title   = LANG_PARSER_USER;
    public $is_public = false;
    public $sql     = 'varchar(255) NULL DEFAULT NULL';
    public $filter_type = 'int';
    public $filter_hint = LANG_PARSER_USER_FILTER_HINT;

    public function getInput($value) {
        return html_input('text', $this->name, $value);
    }

    public function parse($value){
        return '<a href="'.href_to('users', $value['id']).'">'.$value['nickname'].'</a>';
    }

}
