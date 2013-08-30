<?php

class formTemplateOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_PAGE_LOGO,
                'childs' => array(
                    new fieldImage('logo', array(
                        'options' => array(
                            'sizes' => array('small', 'original')
                        )
                    )),
                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_THEME_COPYRIGHT,
                'childs' => array(

                    new fieldString('owner_name', array(
                        'title' => LANG_TITLE
                    )),

                    new fieldString('owner_url', array(
                        'title' => LANG_THEME_COPYRIGHT_URL,
                        'hint' => LANG_THEME_COPYRIGHT_URL_HINT
                    )),

                    new fieldString('owner_year', array(
                        'title' => LANG_THEME_COPYRIGHT_YEAR,
                        'hint' => LANG_THEME_COPYRIGHT_YEAR_HINT
                    )),

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_THEME_LAYOUT,
                'childs' => array(

                    new fieldList('layout_type', array(
                        'title' => LANG_THEME_LAYOUT_WIDTH_TYPE,
                        'default' => 'fixed',
                        'items' => array(
                            'fixed' => LANG_THEME_LAYOUT_WIDTH_TYPE_F,
                            'adaptive' => LANG_THEME_LAYOUT_WIDTH_TYPE_A,
                        )
                    )),

                    new fieldNumber('layout_width', array(
                        'title' => LANG_THEME_LAYOUT_WIDTH,
                        'default' => 100,
                        'rules' => array(
                           array('min', 0),
                           array('max', 100),
                        )
                    )),

                    new fieldNumber('layout_min_width', array(
                        'title' => LANG_THEME_LAYOUT_WIDTH_MIN,
                        'default' => 980,
                    )),

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_THEME_LAYOUT_COLUMNS,
                'childs' => array(

                    new fieldList('aside_pos', array(
                        'title' => LANG_THEME_LAYOUT_SIDEBAR_POS,
                        'default' => 'right',
                        'items' => array(
                            'left' => LANG_THEME_LAYOUT_LEFT,
                            'right' => LANG_THEME_LAYOUT_RIGHT,
                        )
                    )),

                )
            ),


        );

    }

}
