<?php

class fieldImage extends cmsFormField {

    public $title = LANG_PARSER_IMAGE;
    public $sql   = 'text';

    public function getOptions(){
        return array(
            new fieldList('size_teaser', array(
                'title' => LANG_PARSER_IMAGE_SIZE_TEASER,
                'default' => 'small',
                'items' => array(
                    'micro' => LANG_PARSER_IMAGE_SIZE_MICRO,
                    'small' => LANG_PARSER_IMAGE_SIZE_SMALL,
                    'normal' => LANG_PARSER_IMAGE_SIZE_NORMAL,
                    'big' => LANG_PARSER_IMAGE_SIZE_BIG,
                    'original' => LANG_PARSER_IMAGE_SIZE_ORIGINAL
                )
            )),
            new fieldList('size_full', array(
                'title' => LANG_PARSER_IMAGE_SIZE_FULL,
                'default' => 'big',
                'items' => array(
                    'micro' => LANG_PARSER_IMAGE_SIZE_MICRO,
                    'small' => LANG_PARSER_IMAGE_SIZE_SMALL,
                    'normal' => LANG_PARSER_IMAGE_SIZE_NORMAL,
                    'big' => LANG_PARSER_IMAGE_SIZE_BIG,
                    'original' => LANG_PARSER_IMAGE_SIZE_ORIGINAL
                )
            )),
            new fieldListMultiple('sizes', array(
                'title' => LANG_PARSER_IMAGE_SIZE_UPLOAD,
                'default' => 0,
                'items' => array(
                    'micro' => LANG_PARSER_IMAGE_SIZE_MICRO,
                    'small' => LANG_PARSER_IMAGE_SIZE_SMALL,
                    'normal' => LANG_PARSER_IMAGE_SIZE_NORMAL,
                    'big' => LANG_PARSER_IMAGE_SIZE_BIG,
                    'original' => LANG_PARSER_IMAGE_SIZE_ORIGINAL
                )
            )),
        );
    }

    public function parseTeaser($value){

        $config = cmsConfig::getInstance();

        $paths = is_array($value) ? $value : cmsModel::yamlToArray($value);

        if (!$paths && $this->hasDefaultValue()){ $paths = $this->parseDefaultPaths(); }

        if (!$paths){ return; }

        return '<img src="'.$config->upload_host . '/' . $paths[ $this->getOption('size_teaser') ].'" border="0" />';

    }

    public function parse($value){

        $config = cmsConfig::getInstance();

        $paths = is_array($value) ? $value : cmsModel::yamlToArray($value);

        if (!$paths && $this->hasDefaultValue()){ $paths = $this->parseDefaultPaths(); }

        if (!$paths){ return; }

        return '<img src="'.$config->upload_host . '/' . $paths[ $this->getOption('size_full') ].'" border="0" />';

    }

    public function store($value, $is_submitted, $old_value=null){

        $config = cmsConfig::getInstance();

        if (!is_null($old_value) && !is_array($old_value)){

            $old_value = cmsModel::yamlToArray($old_value);

            if ($old_value != $value){
                foreach($old_value as $image_url){
                    $image_path = $config->upload_path . $image_url;
                    @unlink($image_path);
                }
            }

        }

        $sizes = $this->getOption('sizes');

        if (empty($sizes) || empty($value)) { return $value; }

        foreach($value as $size => $image_url){
            if (!in_array($size, $sizes)){
                $image_path = $config->upload_path . $image_url;
                @unlink($image_path);
            }
        }

        return $value;

    }

    public function parseDefaultPaths(){
        $string = $this->getDefaultValue();
        if (!$string) { return false; }
        $items = array();
        $rows = explode("\n", $string);
        if (is_array($rows)){
            foreach($rows as $row){
                $item = explode('|', trim($row));
                $items[trim($item[0])] = trim($item[1]);
            }
        }
        return $items;
    }


}
