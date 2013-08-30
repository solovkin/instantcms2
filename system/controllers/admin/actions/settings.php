<?php

class actionAdminSettings extends cmsAction {

    public function run($do=false){

        // если нужно, передаем управление другому экшену
        if ($do){
            $this->runAction('settings_'.$do, array_slice($this->params, 1));
            return;
        }

        $config = cmsConfig::getInstance();

        $values = $config->getAll();

        $form = $this->getForm('settings');

        $is_submitted = $this->request->has('submit');

        if ($is_submitted){

            $values = array_merge($values, $form->parse($this->request, $is_submitted));
            $errors = $form->validate($this,  $values);

            if (!$errors){

                $result = $config->save($values);

                if (!$result){
                    $errors = array();
                    cmsUser::addSessionMessage(LANG_CP_SETTINGS_NOT_WRITABLE, 'error');
                }

            } else {

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return cmsTemplate::getInstance()->render('settings', array(
            'do' => 'edit',
            'values' => $values,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
