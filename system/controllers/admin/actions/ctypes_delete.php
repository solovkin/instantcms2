<?php

class actionAdminCtypesDelete extends cmsAction {

    public function run($id){

        if (!$id) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($id);

        $content_model->deleteContentType($id);

        cmsCore::getModel('widgets')->deletePagesByName('content', "{$ctype['name']}.*");

        cmsCore::getController('activity')->deleteType('content', "add.{$ctype['name']}");
        
        $this->redirectToAction('ctypes');

    }

}
