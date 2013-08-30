<?php

class onContentUserTabShow extends cmsAction {

    public function run($profile, $tab_name, $ctype_name=false){

        $user = cmsUser::getInstance();
        $template = cmsTemplate::getInstance();

        if (!$ctype_name) { $ctype_name = $this->first_ctype_name; }

        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) { cmsCore::error404(); }

        $this->model->filterEqual('user_id', $profile['id']);

        $page_url = href_to('users', $profile['id'], array('content', $ctype_name));

        if ($user->id != $profile['id'] && !$user->is_admin){
            $this->model->filterHiddenParents();
        }

        if ($user->id == $profile['id'] || $user->is_admin){
            $this->model->disableApprovedFilter();
        }
        
        $list_html = $this->renderItemsList($ctype, $page_url);

        return $template->renderInternal($this, 'profile_tab', array(
            'user' => $user,
            'profile' => $profile,
            'ctype' => $ctype,
            'html' => $list_html
        ));

    }

}
