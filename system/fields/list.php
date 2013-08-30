<?php

class fieldList extends cmsFormField {

    public $title = LANG_PARSER_LIST;
    public $sql   = 'int NULL DEFAULT NULL';
    public $filter_type = 'int';
    public $filter_hint = LANG_PARSER_LIST_FILTER_HINT;

    public function getOptions(){
        return array(
            new fieldCheckbox('filter_multiple', array(
                'title' => LANG_PARSER_LIST_FILTER_MULTI,
                'default' => false
            )),
        );
    }

    public function getFilterInput($value) {

        $items = $this->getListItems();

         if (!$this->getOption('filter_multiple')){

            $items = array_pad($items, (sizeof($items)+1)*-1, '');
            return html_select($this->name, $items, $value);

         } else {

             $value = is_array($value) ? $value : array();
             return html_select_multiple($this->name, $items, $value);

         }


    }

    public function parse($value){

        $items = $this->getListItems();
        $item  = '';

        if (isset($items[$value])) { $item = $items[$value]; }

        return htmlspecialchars($item);

    }

    public function getListItems(){

        $items = array();

        if (isset($this->items)){

            $items = $this->items;

        } else if (isset($this->generator)) {

            $generator = $this->generator;
            $items = $generator($this->item);

        } else if ($this->hasDefaultValue()) {

            $items = $this->parseListItems($this->getDefaultValue());

        }

        return $items;

    }

    public function parseListItems($string){
        $items = array();
        $rows = explode("\n", trim($string));
        if (is_array($rows)){
            foreach($rows as $count=>$row){
                if (mb_strpos($row, '|')){
                    list($index, $value) = explode('|', trim($row));
                } else {
                    $index = $count;
                    $value = $row;
                }
                $items[trim($index)] = trim($value);
            }
        }
        return $items;
    }

    public function applyFilter($model, $value) {

        if (!$this->getOption('filter_multiple')){

            $model->filterEqual($this->name, $value);

        } else {

            if (!is_array($value)) { return $model; }

            $model->filterIn($this->name, $value);

        }

        return $model;


    }

}
