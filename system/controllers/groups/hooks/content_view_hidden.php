<?php

class onGroupsContentViewHidden extends cmsAction {

    public function run($data){

        $viewable = $data['viewable'];
        $item = $data['item'];

        if (!$viewable) { return false; }

        if (!$item['parent_type'] == 'group') { return $viewable; }

        $user = cmsUser::getInstance();

        if (!$user->is_logged){ return false; }

        $membership = $this->model->getMembership($item['parent_id'], $user->id);

        if ($membership === false){ return false; }

        return true;

    }

}
