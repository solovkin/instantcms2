<?php

class actionUsersProfileEdit extends cmsAction {

    public function run($profile, $do=false){
        
        $user = cmsUser::getInstance();

        // если нужно, передаем управление другому экшену
        if ($do){
            $this->runAction('profile_edit_'.$do, array($profile) + array_slice($this->params, 2));
            return;
        }

        // проверяем наличие доступа
        if ($profile['id'] != $user->id && !$user->is_admin) { cmsCore::error404(); }

        // Получаем поля
        $content_model = cmsCore::getModel('content');
        $content_model->setTablePrefix('');
        $content_model->orderBy('ordering');
        $fields = $content_model->getContentFields('users');

        // Строим форму
        $form = new cmsForm();

        // Разбиваем поля по группам
        $fieldsets = cmsForm::mapFieldsToFieldsets($fields, function($field, $user){

            // проверяем что группа пользователя имеет доступ к редактированию этого поля
            if ($field['groups_edit'] && !$user->isInGroups($field['groups_edit'])) { return false; }

            return true;

        });

        // Добавляем поля в форму
        foreach($fieldsets as $fieldset){

            $fieldset_id = $form->addFieldset($fieldset['title']);

            foreach($fieldset['fields'] as $field){

                // добавляем поле в форму
                $form->addField($fieldset_id, $field['handler']);

            }

        }

        // Добавляем поле выбора часового пояса
        $config = cmsConfig::getInstance();
        $fieldset_id = $form->addFieldset( LANG_TIME_ZONE );
        $form->addField($fieldset_id, new fieldList('time_zone', array(
            'default' => $config->time_zone,
            'generator' => function($item){
                return cmsCore::getTimeZones();
            }
        )));

        // Форма отправлена?
        $is_submitted = $this->request->has('submit');

        if ($is_submitted){

            // Парсим форму и получаем поля записи
            $profile = array_merge($profile, $form->parse($this->request, $is_submitted, $profile));

            // Проверям правильность заполнения
            $errors = $form->validate($this,  $profile);

            if (!$errors){
                $is_allowed = cmsEventsManager::hookAll('user_profile_update', $profile, true);
                if ($is_allowed !== true && in_array(false, $is_allowed)) { $errors = true; }
            }

            if (!$errors){

                // Обновляем профиль и редиректим на его просмотр
                $this->model->updateUser($profile['id'], $profile);

                // Отдельно обновляем часовой пояс в сессии
                cmsUser::sessionSet('user_data:time_zone', $profile['time_zone']);

                $this->redirectTo('users', $profile['id']);

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return cmsTemplate::getInstance()->render('profile_edit', array(
            'do' => 'edit',
            'id' => $profile['id'],
            'profile' => $profile,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
