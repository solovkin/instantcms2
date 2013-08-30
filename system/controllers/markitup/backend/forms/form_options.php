<?php

class formMarkitupOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_MARKITUP_THEME,
                'childs' => array(

                    new fieldString('set', array(
                        'title' => LANG_MARKITUP_THEME_SET,
                        'default' => 'default_ru'
                    )),
                    new fieldString('skin', array(
                        'title' => LANG_MARKITUP_THEME_SKIN,
                        'default' => 'simple'
                    )),

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_MARKITUP_IMAGES,
                'childs' => array(

                    new fieldCheckbox('images_upload', array(
                        'title' => LANG_MARKITUP_IMAGES_UPLOAD,
                        'default' => 1
                    )),
                    new fieldNumber('images_w', array(
                        'title' => LANG_MARKITUP_IMAGES_W,
                        'default' => 400
                    )),
                    new fieldNumber('images_h', array(
                        'title' => LANG_MARKITUP_IMAGES_H,
                        'default' => 400
                    )),

                )
            ),

        );

    }

}
