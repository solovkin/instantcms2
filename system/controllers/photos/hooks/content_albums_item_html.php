<?php

class onPhotosContentAlbumsItemHtml extends cmsAction {

    public function run($album){

        $core = cmsCore::getInstance();
        $template = cmsTemplate::getInstance();

        $page = $core->request->get('page', 1);
        $perpage = 16;

        $total = $this->model->getPhotosCount($album['id']);

        $this->model->limitPage($page, $perpage);

        $photos = $this->model->getPhotos($album['id']);

        if (!$photos && $page > 1) { cmsCore::error404(); }

        return $template->renderInternal($this, 'album', array(
            'album' => $album,
            'photos' => $photos,
            'page' => $page,
            'perpage' => $perpage,
            'total' => $total,
            'page_url' => ''
        ));

    }

}
