<?php

class actionCommentsTrack extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $target_controller = $this->request->get('tc');
        $target_subject = $this->request->get('ts');
        $target_id = $this->request->get('ti');
        $is_track = $this->request->get('is_track', 0);

        $template = cmsTemplate::getInstance();

        // Проверяем валидность
        $is_valid = $this->validate_sysname($target_controller) &&
                    $this->validate_sysname($target_subject) &&
                    $this->validate_number($target_id) &&
                    $this->validate_number($is_track);

        if (!$is_valid){
            $template->renderJSON(array('error' => true));
        }

        $user = cmsUser::getInstance();

        $success = $this->model->
                            filterEqual('target_controller', $target_controller)->
                            filterEqual('target_subject', $target_subject)->
                            filterEqual('target_id', $target_id)->
                            toggleTracking($is_track, $user->id, $target_controller, $target_subject, $target_id);

        $template->renderJSON(array('error' => !$success));

    }

}
