<?php

class actionContentItemEdit extends cmsAction {

    public function run(){

        $user = cmsUser::getInstance();

        // Получаем название типа контента и сам тип
        $ctype_name = $this->request->get('ctype_name');
        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) { cmsCore::error404(); }

        $id = $this->request->get('id');
        if (!$id) { cmsCore::error404(); }

        // Получаем нужную запись
        $item = $this->model->getContentItem($ctype['name'], $id);
        if (!$item) { cmsCore::error404(); }

        // проверяем наличие доступа
        if (!cmsUser::isAllowed($ctype['name'], 'edit')) { cmsCore::error404(); }
        if (!cmsUser::isAllowed($ctype['name'], 'edit', 'all')) {
            if (cmsUser::isAllowed($ctype['name'], 'edit', 'own') && $item['user_id'] != $user->id) {
                cmsCore::error404();
            }
        }

        $is_premoderation = $ctype['is_premod_edit'];
        $is_moderator = $user->is_admin || $this->model->userIsContentTypeModerator($ctype_name, $user->id);
        if (!$item['is_approved'] && !$is_moderator) { cmsCore::error404(); }

        // Получаем родительский тип, если он задан
        if ($this->request->has('parent_type')){
            $parent['ctype'] = $this->model->getContentTypeByName($this->request->get('parent_type'));
            $parent['item']  = $this->model->getContentItemBySLUG($parent['ctype']['name'], $this->request->get('parent_slug'));
        }

        // Получаем поля для данного типа контента
        $this->model->orderBy('ordering');
        $fields = $this->model->getContentFields($ctype_name);

        // Строим форму
        $form = new cmsForm();

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
        if (!$ctype['is_auto_url'] && !$ctype['is_fixed_url']){

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
                    'rules' => array( array('max_length', 100) )
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

        // Получаем теги
        if ($ctype['is_tags']){
            $tags_model = cmsCore::getModel('tags');
            $item['tags'] = $tags_model->getTagsStringForTarget($this->name, $ctype['name'], $id);
        }

        // Форма отправлена?
        $is_submitted = $this->request->has('submit');

        if ($is_submitted){

            // Парсим форму и получаем поля записи
            $item = array_merge($item, $form->parse($this->request, $is_submitted, $item));

            // Проверям правильность заполнения
            $errors = $form->validate($this,  $item);

            if (!$errors){

                $item['is_approved'] = $item['is_approved'] && (!$ctype['is_premod_edit'] || $is_moderator);
                $item['approved_by'] = null;

                if ($ctype['is_auto_keys']){ $item['seo_keys'] = string_get_meta_keywords($item['content']); }
                if ($ctype['is_auto_desc']){ $item['seo_desc'] = string_get_meta_description($item['content']); }

                if ($ctype['is_tags']){
                    $tags_model->updateTags($item['tags'], $this->name, $ctype['name'], $id);
                    $item['tags'] = $tags_model->getTagsStringForTarget($this->name, $ctype['name'], $id);
                }

                //
                // Добавляем запись и редиректим на ее просмотр
                //

                $item = cmsEventsManager::hook("content_before_update", $item);
                $item = cmsEventsManager::hook("content_{$ctype['name']}_before_update", $item);

                $item = $this->model->updateContentItem($ctype, $id, $item);

                cmsEventsManager::hook("content_after_update", $item);
                cmsEventsManager::hook("content_{$ctype['name']}_after_update", $item);

                if ($item['is_approved'] || $is_moderator){
                    cmsEventsManager::hook("content_after_update_approve", array('ctype_name'=>$ctype_name, 'item'=>$item));
                    cmsEventsManager::hook("content_{$ctype['name']}_after_update_approve", $item);
                } else {
                    $this->requestModeration($ctype_name, $item, false);
                }

                // обновляем приватность комментариев
                if (isset($item['is_private'])){
                    cmsCore::getModel('comments')->
                                filterEqual('target_controller', $this->name)->
                                filterEqual('target_subject', $ctype_name)->
                                filterEqual('target_id', $item['id'])->
                                updateCommentsPrivacy($item['is_private']);
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
            'do' => 'edit',
            'ctype' => $ctype,
            'parent' => isset($parent) ? $parent : false,
            'item' => $item,
            'form' => $form,
            'is_moderator' => $is_moderator,
            'is_premoderation' => $is_premoderation,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
