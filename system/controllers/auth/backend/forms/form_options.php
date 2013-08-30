<?php

class formAuthOptions extends cmsForm {

    public $is_tabbed = true;

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_REGISTRATION,
                'childs' => array(

                    new fieldCheckbox('reg_captcha', array(
                        'title' => LANG_REG_CFG_REG_CAPTCHA,
                    )),

                    new fieldCheckbox('verify_email', array(
                        'title' => LANG_REG_CFG_VERIFY_EMAIL,
                        'hint' => LANG_REG_CFG_VERIFY_EMAIL_HINT,
                    )),

                    new fieldNumber('verify_exp', array(
                        'title' => LANG_REG_CFG_VERIFY_EXPIRATION,
                        'default' => 48
                    )),

                )
            ),
            
            array(
                'type' => 'fieldset',
                'title' => LANG_AUTHORIZATION,
                'childs' => array(

                    new fieldCheckbox('auth_captcha', array(
                        'title' => LANG_REG_CFG_AUTH_CAPTCHA,
                    )),

                )
            ),

            array(
                'type' => 'fieldset',
                'title' => LANG_AUTH_RESTRICTIONS,
                'childs' => array(

                    new fieldText('restricted_emails', array(
                        'title' => LANG_AUTH_RESTRICTED_EMAILS,
                        'hint' => LANG_AUTH_RESTRICTED_EMAILS_HINT,
                    )),

                    new fieldText('restricted_names', array(
                        'title' => LANG_AUTH_RESTRICTED_NAMES,
                        'hint' => LANG_AUTH_RESTRICTED_NAMES_HINT,
                    )),

                    new fieldText('restricted_ips', array(
                        'title' => LANG_AUTH_RESTRICTED_IPS,
                        'hint' => LANG_AUTH_RESTRICTED_IPS_HINT,
                    )),

                )
            ),

        );

    }

}
