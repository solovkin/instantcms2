<?php

class fieldImages extends cmsFormField {

    public $title = LANG_PARSER_IMAGES;
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

        $images = cmsModel::yamlToArray($value);

        $html = '';

        foreach($images as $paths){
            $html .= '<a href="'.$config->upload_host . '/' . $paths[$this->getOption('size_full')].'"><img src="'.$config->upload_host . '/' . $paths['small'].'" border="0" /></a>';
            break;
        }

        return $html;

    }

    public function parse($value){

        $config = cmsConfig::getInstance();

        $images = cmsModel::yamlToArray($value);

        $html = '';

        foreach($images as $paths){
            $html .= '<a class="img-'.$this->getName().'" href="'.$config->upload_host . '/' . $paths[$this->getOption('size_full')].'"><img src="'.$config->upload_host . '/' . $paths['small'].'" border="0" /></a>';
        }

        $html .= '<script>$(document).ready(function() { icms.modal.bindGallery(".img-'.$this->getName().'"); });</script>';

        return $html;

    }

    public function store($value, $is_submitted, $old_value=null){

        $result = null;

        if (is_array($value)){
            $result = array();
            foreach ($value as $idx=>$paths){ $result[] = $paths; }
        }

        $sizes = $this->getOption('sizes');

        if (empty($sizes) || empty($result)) { return $result; }

        $config = cmsConfig::getInstance();

        foreach($result as $image){
            foreach($image as $size => $image_url){
                if (!in_array($size, $sizes)){
                    $image_path = $config->upload_path . $image_url;
                    @unlink($image_path);
                }
            }
        }
        return $result;

    }

}
