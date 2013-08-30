<?php

class onPhotosContentAlbumsAfterAdd extends cmsAction {

    public function run($album){

        $core = cmsCore::getInstance();

        if (!isset($album['id'])) { return false; }
        if (!$core->request->has('photos')) { return false; }

        $photos = $core->request->get('photos');

        $this->model->assignAlbumId($album['id']);

        $this->model->updateAlbumCoverImage($album['id'], $photos);

        $this->model->updateAlbumPhotosCount($album['id'], sizeof($photos));

        $this->model->updatePhotoTitles($album['id'], $photos);

        return true;

    }

}
