<?php

class fieldHtml extends cmsFormField {

    public $title = LANG_PARSER_HTML;
    public $sql   = 'text';
    public $filter_type = 'str';

    public function hasOptions(){ return true; }

    public function getOptions(){
        return array(
            new fieldList('editor', array(
                'title' => LANG_PARSER_HTML_EDITOR,
                'default' => 'imperavi',
                'generator' => function($item){
                    $items = array();
                    $editors = cmsCore::getWysiwygs();
                    foreach($editors as $editor){ $items[$editor] = $editor; }
                    return $items;
                }
            )),
            new fieldCheckbox('is_html_filter', array(
                'title' => LANG_PARSER_HTML_FILTERING,
            ))
        );
    }

    public function getFilterInput($value) {
        return html_input('text', $this->name, $value);
    }

    public function parse($value){

        if ($this->getOption('is_html_filter')){
            $value = cmsEventsManager::hook('html_filter', $value);
        }

        return $value;

    }

    public function applyFilter($model, $value) {
        return $model->filterLike($this->name, "%{$value}%");
    }

}
