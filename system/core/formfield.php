<?php

class cmsFormField {

    public $name;
    public $element_name = '';
    public $filter_type = false;
    public $filter_hint  = false;

    public $title;
    public $element_title = '';

    public $is_public = true;

    public $sql;
    public $allow_index = true;

    public $item = null;

    public $is_virtual = false;
    public $is_hidden = false;

    public $rules = array();

	function __construct($name, $options=null){

        $this->setName($name);

        $this->class = substr(mb_strtolower(get_called_class()), 5);

        if (is_array($options)){
            foreach($options as $option=>$value){
                $this->{$option} = $value;
            }
            if (isset($options['title'])){
                $this->element_title = $options['title'];
            }
        }

        $this->id = str_replace(':', '_', $name);

    }

    public function getProperty($key){
        return isset($this->{$key}) ? $this->{$key} : false;
    }

    public function getOptions() { return array(); }

    public function getOption($key) {

        if( isset($this->options[ $key ]) ){
            return $this->options[ $key ];
        }

        $options = $this->getOptions();

        foreach($options as $field){
            if ($field->getName() == $key && $field->hasDefaultValue()){
                return $field->getDefaultValue();
            }
        }

    }

    public function setOption($key, $value) { $this->{$key} = $value; }

    public function getTitle(){ return $this->title; }

    public function getName() { return $this->name; }
    public function setName($name) {
        $this->name = $name;

        if (strstr($name, ':')){
            list($key, $subkey) = explode(':', $name);
            $this->element_name = "{$key}[{$subkey}]";
        } else {
            $this->element_name = $name;
        }
    }

    public function getElementName() { return $this->element_name; }


    public function setItem($item) { $this->item = $item; }

    public function getSQL() { return $this->sql; }

    public function getRules(){ return $this->rules; }

    public function hasDefaultValue() { return isset($this->default); }
    public function getDefaultValue() { return $this->hasDefaultValue() ? $this->default : null; }

    public function getInput($value) {
        $this->title = $this->element_title;
        return cmsTemplate::getInstance()->renderFormField($this->class, array(
            'field' => $this,
            'value' => $value
        ));
    }

    public function getFilterInput($value){
        $this->element_title = false;
        return $this->getInput($value);
    }

    public function parse($value){ return false; }
    public function parseTeaser($value){ return $this->parse($value); }

    public function applyFilter($model, $value) { return true; }

    public function store($value, $is_submitted, $old_value=null){
       return $value;
    }

}
