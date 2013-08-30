<?php

class actionPhotosView extends cmsAction{

    public function run($id = false){

        if (!$id) { cmsCore::error404(); }

        $photo = $this->model->getPhoto($id);
        if (!$photo){ cmsCore::error404(); }

        $album = $this->model->getAlbum($photo['album_id']);
        if (!$album){ cmsCore::error404(); }

        $photos = $this->model->getPhotos($album['id']);

        $ctype = $album['ctype'];

        $template = cmsTemplate::getInstance();
        $user = cmsUser::getInstance();

        // Рейтинг
        if ($ctype['is_rating']){

            $rating_controller = cmsCore::getController('rating', new cmsRequest(array(
                'target_controller' => $this->name,
                'target_subject' => 'photo'
            ), cmsRequest::CTX_INTERNAL));

            $is_rating_allowed = cmsUser::isAllowed($ctype['name'], 'rate') && ($photo['user_id'] != $user->id);

            $photo['rating_widget'] = $rating_controller->getWidget($photo['id'], $photo['rating'], $is_rating_allowed);

        }

        // Комментарии
        if ($ctype['is_comments']){

            $comments_controller = cmsCore::getController('comments', new cmsRequest(array(
                'target_controller' => $this->name,
                'target_subject' => 'photo',
                'target_id' => $photo['id']
            ), cmsRequest::CTX_INTERNAL));

            $photo['comments_widget'] = $comments_controller->getWidget();

        }

        return $template->render('view', array(
            'photo' => $photo,
            'photos' => $photos,
            'album' => $album,
            'ctype' => $ctype
        ));

    }

}
