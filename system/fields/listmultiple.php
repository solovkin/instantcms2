<?php

class fieldListMultiple extends cmsFormField {

    public $title = LANG_PARSER_LIST_MULTIPLE;
    public $is_public = false;
    public $sql   = 'text NULL DEFAULT NULL';

    public function getOptions(){
        return array(
            new fieldCheckbox('show_all', array(
                'title' => LANG_PARSER_LIST_MULTIPLE_SHOW_ALL,
                'default' => 1
            )),
        );
    }
    
}
