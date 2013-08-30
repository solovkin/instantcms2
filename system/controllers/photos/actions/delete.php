<?php

class actionPhotosDelete extends cmsAction{

    public function run($photo_id = null){

        if (!$photo_id) { cmsCore::error404(); }

        $photo = $this->model->getPhoto($photo_id);

        $success = true;

        // проверяем наличие доступа
        $user = cmsUser::getInstance();
        if (!cmsUser::isAllowed('albums', 'edit')) { $success = false; }
        if (!cmsUser::isAllowed('albums', 'edit', 'all') && $photo['user_id'] != $user->id) { $success = false; }

        if (!$success){
            cmsTemplate::getInstance()->renderJSON(array(
                'success' => false
            ));
        }

        $this->model->deletePhoto($photo_id);

        cmsTemplate::getInstance()->renderJSON(array(
            'success' => true
        ));

    }

}
