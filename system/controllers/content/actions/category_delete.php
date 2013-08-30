<?php

class actionContentCategoryDelete extends cmsAction {

    public function run(){

        // Получаем название типа контента и сам тип
        $ctype_name = $this->request->get('ctype_name');
        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) { cmsCore::error404(); }

        // проверяем наличие доступа
        if (!cmsUser::isAllowed($ctype['name'], 'delete_cat')) { cmsCore::error404(); }

        $id = $this->request->get('id');
        if (!$id) { cmsCore::error404(); }

        $category = $this->model->getCategory($ctype_name, $id);

        $parent = $category['path'][ sizeof($category['path']) - 2 ];

        $this->model->deleteCategory($ctype_name, $id);

        $items = $this->model->filterCategory($ctype_name, $id, true)->getContentItems($ctype_name);

        foreach($items as $item){
            $this->model->deleteContentItem($ctype_name, $item['id']);
        }

        $back_url = $this->request->get('back');

        if ($back_url){
            $this->redirect($back_url);
        } else {
            if ($ctype['options']['list_on']){
                $this->redirectTo($ctype_name, $parent['slug']);
            } else {
                $this->redirectToHome();
            }
        }

    }

}
