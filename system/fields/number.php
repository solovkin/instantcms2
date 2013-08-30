<?php

class fieldNumber extends cmsFormField {

    public $title   = LANG_PARSER_NUMBER;
    public $sql     = 'int(11) NULL DEFAULT NULL';
    public $filter_type = 'int';

    public function getOptions(){
        return array(
            new fieldCheckbox('filter_range', array(
                'title' => LANG_PARSER_NUMBER_FILTER_RANGE,
                'default' => false
            )),
        );
    }

    public function getRules() {

        $this->rules[] = array('digits');

        return $this->rules;

    }

    public function parse($value){
        return htmlspecialchars($value);
    }

    public function getFilterInput($value) {

        if ($this->getOption('filter_range')){

            $from = !empty($value['from']) ? intval($value['from']) : false;
            $to = !empty($value['to']) ? intval($value['to']) : false;

            return LANG_FROM . ' ' . html_input('text', $this->element_name.'[from]', $from, array('class'=>'input-small')) . ' ' .
                    LANG_TO . ' ' . html_input('text', $this->element_name.'[to]', $to, array('class'=>'input-small'));

        } else {

            return parent::getFilterInput($value);

        }

    }

    public function applyFilter($model, $value) {

        if (!$this->getOption('filter_range')){

            $model->filterEqual($this->name, "{$value}");

        } else {

            if (!is_array($value)) { return $model; }

            if (!empty($value['from'])){
                $model->filterGtEqual($this->name, $value['from']);
            }
            if (!empty($value['to'])){
                $model->filterLtEqual($this->name, $value['to']);
            }

        }

        return $model;

    }

}
