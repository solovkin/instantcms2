<?php

class actionContentItemView extends cmsAction {

    public function run(){

        $user = cmsUser::getInstance();

        // Получаем название типа контента и сам тип
        $ctype_name = $this->request->get('ctype_name');
        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) { cmsCore::error404(); }
        if (!$ctype['options']['item_on']) { cmsCore::error404(); }

        // Получаем SLUG записи
        $slug = $this->request->get('slug');

        // Получаем запись
        $item = $this->model->getContentItemBySLUG($ctype_name, $slug);
        if (!$item) { cmsCore::error404(); }

        // Проверяем прохождение модерации
        $is_moderator = false;
        if (!$item['is_approved']){
            $is_moderator = $user->is_admin || $this->model->userIsContentTypeModerator($ctype_name, $user->id);
            if (!$is_moderator && $user->id != $item['user_id']){ cmsCore::error404(); }
        }

        // Проверяем приватность
        if ($item['is_private']){
            $is_friend = $user->isFriend($item['user_id']);
            $is_can_view_private = cmsUser::isAllowed($ctype['name'], 'view_all');
            if (!$is_friend && !$is_can_view_private && !$is_moderator){ cmsCore::error404(); }
        }

        // Проверяем ограничения доступа из других контроллеров
        if ($item['is_parent_hidden']){
            $is_parent_viewable = cmsEventsManager::hook("content_view_hidden", array('viewable'=>true, 'item'=>$item));
            if (!$is_parent_viewable){
                cmsUser::goLogin();
            }
        }

        $item['ctype_name'] = $ctype_name;

        if ($ctype['is_cats'] && $item['category_id'] > 1){
            $item['category'] = $this->model->getCategory($ctype_name, $item['category_id']);
        }

        // Получаем поля для данного типа контента
        $fields = $this->model->getContentFields($ctype_name);

        // Парсим значения полей
        foreach($fields as $name=>$field){
            $fields[ $name ]['html'] = $field['handler']->parse( $item[$name] );
        }

        // Рейтинг
        if ($ctype['is_rating']){

            $rating_controller = cmsCore::getController('rating', new cmsRequest(array(
                'target_controller' => $this->name,
                'target_subject' => $ctype['name']
            ), cmsRequest::CTX_INTERNAL));

            $is_rating_allowed = cmsUser::isAllowed($ctype['name'], 'rate') && ($item['user_id'] != $user->id);

            $item['rating_widget'] = $rating_controller->getWidget($item['id'], $item['rating'], $is_rating_allowed);

        }

        // Комментарии
        if ($ctype['is_comments'] && $item['is_approved']){

            $comments_controller = cmsCore::getController('comments', new cmsRequest(array(
                'target_controller' => $this->name,
                'target_subject' => $ctype['name'],
                'target_id' => $item['id']
            ), cmsRequest::CTX_INTERNAL));

            $item['comments_widget'] = $comments_controller->getWidget();

        }

        // Получаем теги
        if ($ctype['is_tags']){
            $tags_model = cmsCore::getModel('tags');
            $item['tags'] = $tags_model->getTagsForTarget($this->name, $ctype['name'], $item['id']);
        }

        // Информация о модераторе для админа и владельца записи
        if ($item['approved_by'] && ($user->is_admin || $user->id == $item['user_id'])){
            $item['approved_by'] = cmsCore::getModel('users')->getUser($item['approved_by']);
        }

        return cmsTemplate::getInstance()->render('item_view', array(
            'ctype' => $ctype,
            'fields' => $fields,
            'item' => $item,
            'is_moderator' => $is_moderator,
            'user' => $user
        ));

    }

}
