<?php
class cmsWidget {

    public $name;
    public $controller;
    public $title;
    public $is_title;
    public $position;
    public $groups_view;
    public $groups_hide;
    public $options;
    public $css_class;

    public $is_cacheable = true;

    private $template;
    private $wrapper = 'wrapper';

    public function __construct($widget){

        $this->name = $widget['name'];
        $this->controller = $widget['controller'];

        $form = cmsCore::getWidgetOptionsForm($this->name, $this->controller);
        $data = $form->parse(new cmsRequest($widget), true);

        foreach($data as $field => $value){
            $this->{$field} = $value;
        }

        $this->css_class = $widget['class'];

        $this->links = $widget['links'];

        $this->position = $widget['position'];
        $this->template = $this->name;

    }

    public function getOption($key, $default=false){
        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }

    public function setTemplate($template){
        $this->template = $template;
    }

    public function getTemplate(){
        return $this->template;
    }

    public function setWrapper($template){
        $this->wrapper = $template;
    }

    public function getWrapper(){
        return $this->wrapper;
    }

    public function disableCache(){
        $this->is_cacheable = false;
    }

    public function enableCache(){
        $this->is_cacheable = true;
    }

    public function isCacheable(){
        return $this->is_cacheable;
    }

}
