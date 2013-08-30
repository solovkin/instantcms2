<?php
class content extends cmsFrontend {

    const perpage = 15;

//============================================================================//
//============================================================================//

    public function before($action_name) {

        parent::before($action_name);

        $core = cmsCore::getInstance();

        // Запрещаем прямой вызов этого контроллера
        if ($core->uri_controller == 'content') { cmsCore::error404(); }

    }

//============================================================================//
//============================================================================//

    public function route($uri){

        $core = cmsCore::getInstance();

        $uri = $core->uri_controller . '/' . $uri;

        $action_name = $this->parseRoute($uri);

        if (!$action_name) { cmsCore::error404(); }

        $this->runAction($action_name);

    }

//============================================================================//
//============================================================================//

    public function getMenuAddItems($menu_item_id){

        $result = array('url' => '#', 'items' => false);

        $ctypes = $this->model->getContentTypes();

        if (!$ctypes) { return $result; }

        foreach($ctypes as $id=>$ctype){

            if (!cmsUser::isAllowed($ctype['name'], 'add')) { continue; }

            if (!empty($ctype['labels']['create'])){

                $result['items'][] = array(
                    'id' => 'content_add' . $ctype['id'],
                    'parent_id' =>  $menu_item_id,
                    'title' => sprintf(LANG_CONTENT_ADD_ITEM, $ctype['labels']['create']),
                    'childs_count' => 0,
                    'url' => href_to($ctype['name'], 'add')
                );

            }

        }

        return $result;

    }


//============================================================================//

    public function getMenuCategoriesItems($menu_item_id, $ctype){

        $result = array('url' => href_to($ctype['name']), 'items' => false);

        if (!$ctype['is_cats']) { return $result; }

        $tree = $this->model->getCategoriesTree($ctype['name']);

        if (!$tree) { return $result; }

        foreach($tree as $id=>$cat){

            if ($cat['id']==1) { continue; }

            $result['items'][] = array(
                'id' => 'content' . $cat['id'],
                'parent_id' =>  $cat['parent_id']==1 ?
                                $menu_item_id :
                                'content'.$cat['parent_id'],
                'title' => $cat['title'],
                'childs_count' => ($cat['ns_right'] - $cat['ns_left']) -1,
                'url' => href_to($ctype['name'], $cat['slug'])
            );

        }

        return $result;

    }

//============================================================================//
//============================================================================//

    public function renderItemsList($ctype, $page_url, $hide_filter=false){

        $user = cmsUser::getInstance();

        // Получаем поля для данного типа контента
        $fields = cmsCore::getModel('content')->getContentFields($ctype['name']);

        $page = $this->request->get('page', 1);
        $perpage = self::perpage;

        $filters = array();

        if ($hide_filter) { $ctype['options']['list_show_filter'] = false; }

        // Если для данного контента включен фильтр
        if ($ctype['options']['list_show_filter']){

            // проверяем запросы фильтрации по полям
            foreach($fields as $name => $field){

                if (!$field['is_in_filter']) { continue; }
                if (!$this->request->has($name)){ continue; }

                $value = $this->request->get($name);
                if (!$value) { continue; }

                $this->model = $field['handler']->applyFilter($this->model, $value);
                $filters[$name] = $value;

            }

        }

        // Отключаем фильтр приватности для тех кому это разрешено
        if (cmsUser::isAllowed($ctype['name'], 'view_all')) {
            $this->model->disablePrivacyFilter();
        }

        // Постраничный вывод
        $this->model->limitPage($page, $perpage);

        // Получаем количество и список записей
        $total = $this->model->getContentItemsCount($ctype['name']);
        $items = $this->model->getContentItems($ctype['name']);

        // Рейтинг
        if ($ctype['is_rating'] && $total){

            $rating_controller = cmsCore::getController('rating', new cmsRequest(array(
                'target_controller' => $this->name,
                'target_subject' => $ctype['name']
            ), cmsRequest::CTX_INTERNAL));

            $is_rating_allowed = cmsUser::isAllowed($ctype['name'], 'rate');

            foreach($items as $id=>$item){
                $is_rating_enabled = $is_rating_allowed && ($item['user_id'] != $user->id);
                $items[$id]['rating_widget'] = $rating_controller->getWidget($item['id'], $item['rating'], $is_rating_enabled);
            }

        }

        $items = cmsEventsManager::hook("content_{$ctype['name']}_before_list", $items);

        $template = cmsTemplate::getInstance();

        $template->setContext($this);

        $html = $template->renderContentList($ctype['name'], array(
            'page_url' => $page_url,
            'ctype' => $ctype,
            'fields' => $fields,
            'filters' => $filters,
            'page' => $page,
            'perpage' => $perpage,
            'total' => $total,
            'items' => $items,
            'user' => $user,
        ), new cmsRequest(array(), cmsRequest::CTX_INTERNAL));

        $template->restoreContext();

        return $html;

    }

//============================================================================//
//============================================================================//

    public function getPermissionsSubjects(){

        $ctypes = $this->model->getContentTypes();

        $subjects = array();

        foreach($ctypes as $ctype){
            $subjects[] = array(
                'name' => $ctype['name'],
                'title' => $ctype['title']
            );
        }

        return $subjects;

    }


//============================================================================//
//============================================================================//

    public function addWidgetsPages($ctype){

        $widgets_model = cmsCore::getModel('widgets');

        $widgets_model->addPage(array(
            'controller' => 'content',
            'name' => "{$ctype['name']}.all",
            'title_const' => 'LANG_WP_CONTENT_ALL_PAGES',
            'title_subject' => $ctype['title'],
            'url_mask' => array(
                "{$ctype['name']}",
                "{$ctype['name']}-*",
                "{$ctype['name']}/*",
            )
        ));

        $widgets_model->addPage(array(
            'controller' => 'content',
            'name' => "{$ctype['name']}.list",
            'title_const' => 'LANG_WP_CONTENT_LIST',
            'title_subject' => $ctype['title'],
            'url_mask' => array(
                "{$ctype['name']}",
                "{$ctype['name']}-*",
                "{$ctype['name']}/*",
            ),
            'url_mask_not' => array(
                "{$ctype['name']}/*.html",
                "{$ctype['name']}/add",
                "{$ctype['name']}/edit/*",
            )
        ));

        $widgets_model->addPage(array(
            'controller' => 'content',
            'name' => "{$ctype['name']}.item",
            'title_const' => 'LANG_WP_CONTENT_ITEM',
            'title_subject' => $ctype['title'],
            'url_mask' => "{$ctype['name']}/*.html"
        ));

        $widgets_model->addPage(array(
            'controller' => 'content',
            'name' => "{$ctype['name']}.edit",
            'title_const' => 'LANG_WP_CONTENT_ITEM_EDIT',
            'title_subject' => $ctype['title'],
            'url_mask' => array(
                "{$ctype['name']}/add",
                "{$ctype['name']}/edit/*"
            )
        ));

        return true;

    }

//============================================================================//
//============================================================================//

    public function requestModeration($ctype_name, $item, $is_new_item=true){

        $moderator_id = $this->model->getNextModeratorId($ctype_name);

        $users_model = cmsCore::getModel('users');

        $moderator = $users_model->getUser($moderator_id);
        $author = $users_model->getUser($item['user_id']);

        // добавляем задачу модератору
        $this->model->addModeratorTask($ctype_name, $moderator_id, $is_new_item, $item);

        // отправляем письмо модератору
        $messenger = cmsCore::getController('messages');
        $to = array('email' => $moderator['email'], 'name' => $moderator['nickname']);
        $letter = array('name' => 'moderation');

        $messenger->sendEmail($to, $letter, array(
            'moderator' => $moderator['nickname'],
            'author' => $author['nickname'],
            'author_url' => href_to_abs('users', $author['id']),
            'page_title' => $item['title'],
            'page_url' => href_to_abs($ctype_name, $item['slug'] . ".html"),
            'date' => html_date_time(),
        ));

        cmsUser::addSessionMessage(sprintf(LANG_MODERATION_IDLE, $moderator['nickname']), 'info');

    }

//============================================================================//
//============================================================================//

}
