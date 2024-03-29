<?php

class actionAdminCtypesPerms extends cmsAction {

    public function run($ctype_id){

        if (!$ctype_id) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);
        if (!$ctype) { cmsCore::error404(); }

        cmsCore::loadControllerLanguage('content');

        $rules  = cmsPermissions::getRulesList('content');
        $values = cmsPermissions::getPermissions($ctype['name']);

        $users_model = cmsCore::getModel('users');
        $groups = $users_model->getGroups(false);

        return cmsTemplate::getInstance()->render('ctypes_perms', array(
            'ctype' => $ctype,
            'rules' => $rules,
            'values' => $values,
            'groups' => $groups,
        ));

    }

}
