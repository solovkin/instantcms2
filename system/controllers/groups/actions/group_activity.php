<?php

class actionGroupsGroupActivity extends cmsAction {

    public function run($group){

        $user = cmsUser::getInstance();

        $activity_controller = cmsCore::getController('activity', $this->request);

        $activity_controller->model->filterEqual('group_id', $group['id']);

        $page_url = href_to($this->name, $group['id'], 'activity');

        $html = $activity_controller->renderActivityList($page_url);

        return cmsTemplate::getInstance()->render('group_activity', array(
            'user' => $user,
            'group' => $group,
            'html' => $html
        ));

    }

}
