<?php

class actionCommentsSubmit extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $action = $this->request->get('action');

        if ($action=='add' && !cmsUser::isAllowed('comments', 'add')){ cmsCore::error404(); }
        if ($action=='update' && !cmsUser::isAllowed('comments', 'edit')){ cmsCore::error404(); }

        $template = cmsTemplate::getInstance();

        $user = cmsUser::getInstance();

        $csrf_token = $this->request->get('csrf_token');
        $target_controller = $this->request->get('tc');
        $target_subject = $this->request->get('ts');
        $target_id = $this->request->get('ti');
        $parent_id = $this->request->get('parent_id');
        $comment_id = $this->request->get('id');
        $content = $this->request->get('content');

        // Проверяем валидность
        $is_valid = $this->validate_sysname($target_controller) &&
                    $this->validate_sysname($target_subject) &&
                    $this->validate_number($target_id) &&
                    $this->validate_number($parent_id) &&
                    (!$comment_id || $this->validate_number($comment_id)) &&
                    cmsForm::validateCSRFToken($csrf_token, false) &&
                    in_array($action, array('add', 'preview', 'update'));

        if (!$is_valid){
            $result = array('error' => true, 'message' => LANG_COMMENT_ERROR);
            $template->renderJSON($result);
        }

        // Типографируем текст
        $content_html = cmsEventsManager::hook('html_filter', $content);

        //
        // Превью комментария
        //
        if ($action=='preview'){
            $result = array('error' => false, 'html' => $content_html);
            $template->renderJSON($result);
        }

        //
        // Редактирование комментария
        //
        if ($action=='update'){

            $comment = $this->model->getComment($comment_id);

            if (!cmsUser::isAllowed('comments', 'edit', 'all')) {
                if (cmsUser::isAllowed('comments', 'edit', 'own') && $comment['user']['id'] != $user->id) {
                    $result = array('error' => true, 'message' => LANG_COMMENT_ERROR);
                    $template->renderJSON($result);
                }
            }

           $this->model->updateCommentContent($comment_id, $content, $content_html);

           $comment_html = $content_html;

        }

        //
        // Добавление комментария
        //
        if ($action=='add'){

            // Собираем данные комментария
            $comment = array(
                'user_id' => $user->id,
                'parent_id' => $parent_id,
                'target_controller' => $target_controller,
                'target_subject' => $target_subject,
                'target_id' => $target_id,
                'content' => $content,
                'content_html' => $content_html,
            );

            // Получаем модель целевого контроллера
            $target_model = cmsCore::getModel( $target_controller );

            // Получаем URL и заголовок комментируемой страницы
            $target_info = $target_model->getTargetItemInfo($target_subject, $target_id);

            if ($target_info){

                $comment['target_url'] = $target_info['url'];
                $comment['target_title'] = $target_info['title'];

                // Сохраняем комментарий
                $comment_id = $this->model->addComment($comment);

            }

            if ($comment_id){

                // Получаем и рендерим добавленный комментарий
                $comment = $this->model->getComment($comment_id);
                $comment_html = $template->render('comment', array(
                    'comments' => array($comment),
                    'user'=>$user
                ), new cmsRequest(array(), cmsRequest::CTX_INTERNAL));

                // Уведомляем модель целевого контента об изменении количества комментариев
                $comments_count = $this->model->
                                            filterEqual('target_controller', $target_controller)->
                                            filterEqual('target_subject', $target_subject)->
                                            filterEqual('target_id', $target_id)->
                                            getCommentsCount();

                $target_model->updateCommentsCount($target_subject, $target_id, $comments_count);

                $parent_comment = $parent_id ? $this->model->getComment($parent_id) : false;

                // Уведомляем подписчиков
                $this->notifySubscribers($comment, $parent_comment);

                // Уведомляем об ответе на комментарий
                if ($parent_comment){ $this->notifyParent($comment, $parent_comment); }

            }

        }

        // Формируем и возвращаем результат
        $result = array(
            'error' => $comment_id ? false : true,
            'message' => $comment_id ? LANG_COMMENT_SUCCESS : LANG_COMMENT_ERROR,
            'id' => $comment_id,
            'parent_id' => isset($comment['parent_id']) ? $comment['parent_id'] : 0,
            'level' => isset($comment['level']) ? $comment['level'] : 0,
            'html' => isset($comment_html) ? $comment_html : false
        );

        $template->renderJSON($result);

    }

}
