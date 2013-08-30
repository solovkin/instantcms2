<?php

class actionAdminMenuDelete extends cmsAction {

    public function run($id){

        if (!$id) { cmsCore::error404(); }

        $menu_model = cmsCore::getModel('menu');

        $menu_model->deleteMenu($id);

        cmsUser::setCookiePublic('menu_tree_path', '1.0');

        $this->redirectToAction('menu');

    }

}
