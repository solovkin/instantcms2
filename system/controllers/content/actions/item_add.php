<?php

class actionContentItemAdd extends cmsAction {

    public function run(){

        $user = cmsUser::getInstance();

        // Получаем название типа контента
        $ctype_name = $this->request->get('ctype_name');

        // проверяем наличие доступа
        if (!cmsUser::isAllowed($ctype_name, 'add')) { cmsCore::error404(); }

        // Получаем тип контента
        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) { cmsCore::error404(); }

        // проверяем что не превышен лимит на число записей
        $user_items_count = $this->model->getUserContentItemsCount($ctype_name, $user->id);

        if (cmsUser::isPermittedLimitReached($ctype_name, 'limit', $user_items_count, false)){
            cmsUser::addSessionMessage(sprintf(LANG_CONTENT_COUNT_LIMIT, $ctype['labels']['many']), 'error');
            $this->redirectBack();
        }

        $groups_model = cmsCore::getModel('groups');
        $groups = $groups_model->getUserGroups($user->id);

        if (!$groups && $ctype['is_in_groups_only']){
            cmsUser::addSessionMessage(sprintf(LANG_CONTENT_IS_IN_GROUPS_ONLY, $ctype['labels']['many']), 'error');
            $this->redirectBack();
        }

        // Получаем поля для данного типа контента
        $this->model->orderBy('ordering');
        $fields = $this->model->getContentFields($ctype_name);

        // Строим форму
        $form = new cmsForm();
        $fieldset_id = $form->addFieldset();

        // Если включены категории, добавляем в форму поле выбора категории
        if ($ctype['is_cats']){
            $fieldset_id = $form->addFieldset(LANG_CATEGORY);
            $form->addField($fieldset_id,
                new fieldList('category_id', array(
                        'generator' => function($item){
                            $content_model = cmsCore::getModel('content');
                            $tree = $content_model->getCategoriesTree($item['ctype_name']);
                            foreach($tree as $c){
                                $items[$c['id']] = str_repeat('- ', $c['ns_level']).' '.$c['title'];
                            }
                            return $items;
                        }
                    )
                )
            );
            $category_id = $this->request->get('to_id');
        }

        // Если этот контент можно создавать в группах (сообществах) то добавляем
        // поле выбора группы
        $groups_list = array();

        if ($ctype['is_in_groups'] || $ctype['is_in_groups_only']){

            $groups_list = ($ctype['is_in_groups_only']) ? array() : array('0'=>'');
            $groups_list = $groups_list + array_collection_to_list($groups, 'id', 'title');

            $fieldset_id = $form->addFieldset(LANG_GROUP);
            $form->addField($fieldset_id,
                new fieldList('parent_id', array(
                        'items' => $groups_list
                    )
                )
            );

        }

        // Разбиваем поля по группам
        $fieldsets = cmsForm::mapFieldsToFieldsets($fields, function($field, $user){

            // пропускаем системные поля
            if ($field['is_system']) { return false; }

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

        // Если ручной ввод SLUG, то добавляем поле для этого
        if (!$ctype['is_auto_url']){

            $fieldset_id = $form->addFieldset( LANG_SLUG );
            $form->addField($fieldset_id, new fieldString('slug', array(
                'prefix' => '/'.$ctype['name'].'/',
                'suffix' => '.html',
                'rules' => array( array('required') )
            )));

        }

        // Если разрешено управление видимостью, то добавляем поле
        if (cmsUser::isAllowed($ctype_name, 'privacy')) {

            $fieldset_id = $form->addFieldset( LANG_PRIVACY );
            $form->addField($fieldset_id, new fieldList('is_private', array(
                'items' => array(
                    0 => LANG_PRIVACY_PUBLIC,
                    1 => LANG_PRIVACY_PRIVATE,
                ),
                'rules' => array( array('number') )
            )));

        }

        //
        // Если ручной ввод ключевых слов или описания, то добавляем поля для этого
        //
        if (!$ctype['is_auto_keys'] || !$ctype['is_auto_desc']){
            $fieldset_id = $form->addFieldset( LANG_SEO );
            if (!$ctype['is_auto_keys']){
                $form->addField($fieldset_id, new fieldString('seo_keys', array(
                    'title' => LANG_SEO_KEYS,
                    'hint' => LANG_SEO_KEYS_HINT,
                )));
            }
            if (!$ctype['is_auto_desc']){
                $form->addField($fieldset_id, new fieldText('seo_desc', array(
                    'title' => LANG_SEO_DESC,
                    'hint' => LANG_SEO_DESC_HINT,
                )));
            }
        }

        //
        // Если включены теги, то добавляем поле для них
        //
        if ($ctype['is_tags']){
            $fieldset_id = $form->addFieldset( LANG_TAGS );
            $form->addField($fieldset_id, new fieldString('tags', array(
                'hint' => LANG_TAGS_HINT,
                'autocomplete' => array(
                    'multiple' => true,
                    'url' => href_to('tags', 'autocomplete')
                )
            )));
        }

        $is_moderator = $user->is_admin || $this->model->userIsContentTypeModerator($ctype_name, $user->id);
        $is_premoderation = $ctype['is_premod_add'];

        $form = cmsEventsManager::hook("content_{$ctype['name']}_form", $form);

        // Форма отправлена?
        $is_submitted = $this->request->has('submit');

        // Парсим форму и получаем поля записи
        $item = $form->parse($this->request, $is_submitted);

        if (!$is_submitted && !empty($category_id)) { $item['category_id'] = $category_id; }

        if ($is_submitted){

            // Проверям правильность заполнения
            $errors = $form->validate($this,  $item);

            if (!$errors){

                $item['is_approved'] = !$ctype['is_premod_add'] || $is_moderator;

                $item['parent_type'] = null;
                $item['parent_title'] = null;
                $item['parent_url'] = null;

                if (isset($item['parent_id'])){
                    if (array_key_exists($item['parent_id'], $groups_list) && $item['parent_id'] > 0){
                        $item['parent_type'] = 'group';
                        $item['parent_title'] = $groups_list[$item['parent_id']];
                        $item['parent_url'] = href_to_rel('groups', $item['parent_id'], array('content', $ctype_name));
                    } else {
                        $item['parent_id'] = null;
                    }
                }

                if ($ctype['is_auto_keys']){ $item['seo_keys'] = string_get_meta_keywords($item['content']); }
                if ($ctype['is_auto_desc']){ $item['seo_desc'] = string_get_meta_description($item['content']); }

                $item = cmsEventsManager::hook("content_before_add", $item);
                $item = cmsEventsManager::hook("content_{$ctype['name']}_before_add", $item);

                $item = $this->model->addContentItem($ctype, $item);

                if ($ctype['is_tags']){
                    $tags_model = cmsCore::getModel('tags');
                    $tags_model->addTags($item['tags'], $this->name, $ctype['name'], $item['id']);
                    $item['tags'] = $tags_model->getTagsStringForTarget($this->name, $ctype['name'], $item['id']);
                    $this->model->updateContentItemTags($ctype['name'], $item['id'], $item['tags']);
                }

                cmsEventsManager::hook("content_after_add", $item);
                cmsEventsManager::hook("content_{$ctype['name']}_after_add", $item);

                if ($item['is_approved']){
                    cmsEventsManager::hook("content_after_add_approve", array('ctype_name'=>$ctype_name, 'item'=>$item));
                    cmsEventsManager::hook("content_{$ctype['name']}_after_add_approve", $item);
                } else {
                    $this->requestModeration($ctype_name, $item);
                }

                $back_url = $this->request->get('back');

                if ($back_url){
                    $this->redirect($back_url);
                } else {
                    $this->redirectTo($ctype_name, $item['slug'] . '.html');
                }

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return cmsTemplate::getInstance()->render('item_form', array(
            'do' => 'add',
            'parent' => isset($parent) ? $parent : false,
            'ctype' => $ctype,
            'item' => $item,
            'form' => $form,
            'is_moderator' => $is_moderator,
            'is_premoderation' => $is_premoderation,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
