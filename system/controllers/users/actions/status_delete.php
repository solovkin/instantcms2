<?php

class actionUsersStatusDelete extends cmsAction {

    public function run($user_id){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $user = cmsUser::getInstance();

        if ($user->id != $user_id && !$user->is_admin){
            $result = array( 'error' => true, 'message' => LANG_ERROR );
            cmsTemplate::getInstance()->renderJSON($result);
        }

        $this->model->clearUserStatus($user_id);

        $result = array(
            'error' => false,
        );

        cmsTemplate::getInstance()->renderJSON($result);

    }

}
