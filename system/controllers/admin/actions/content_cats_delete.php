<?php

class actionAdminContentCatsDelete extends cmsAction {

    public function run($ctype_id, $category_id=false){

        if (!$category_id) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);

        $category = $content_model->getCategory($ctype['name'], $category_id);

        $url = href_to($ctype['name'], 'delcat', $category_id) . '?back=' . href_to($this->name, 'content');

        $tree_path = $category['parent_id'] == 1 ? "{$ctype_id}.1" : "/{$ctype_id}.1/{$ctype_id}.{$category['parent_id']}";

        cmsUser::setCookiePublic('content_tree_path', $tree_path);

        $this->redirect($url);

    }

}
