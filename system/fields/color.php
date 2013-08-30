<?php

class fieldColor extends cmsFormField {

    public $title   = LANG_PARSER_CITY;
    public $sql     = 'varchar(7) NULL DEFAULT NULL';
    public $filter_type = 'str';
    public $filter_hint = '#RRGGBB';

    public function parse($value){
        return '<div class="color-block" style="background:{'.$value.'}"></div>';
    }

    public function applyFilter($model, $value) {
        return $model->filterEqual($this->name, "{$value}");
    }

}
