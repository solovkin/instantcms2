<?php

class actionPhotosUpload extends cmsAction{

    public function run($album_id = null){

        $config = cmsConfig::getInstance();

        $uploader = new cmsUploader();

        $result = $uploader->upload('qqfile');

        if (!$result['success']){
            cmsTemplate::getInstance()->renderJSON($result);
            $this->halt();
        }

        $result['paths'] = array(
            'big' => $uploader->resizeImage($result['path'], array('width'=>600, 'height'=>460, 'square'=>false)),
            'normal' => $uploader->resizeImage($result['path'], array('width'=>160, 'height'=>160, 'square'=>true)),
            'small' => $uploader->resizeImage($result['path'], array('width'=>64, 'height'=>64, 'square'=>true))
        );

        $result['filename'] = basename($result['path']);

        unset($result['path']);

        $result['url'] = $config->upload_host . '/' . $result['paths']['small'];

        $result['id'] = $this->model->addPhoto($album_id, $result['paths']);

        cmsTemplate::getInstance()->renderJSON($result);
        die('222');
        $this->halt();

    }

}

