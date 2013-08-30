<?php
class images extends cmsFrontend {

//============================================================================//
//============================================================================//

    public function actionGetSingleUploadWidget(){

        if (!$this->request->isInternal()) { cmsCore::error404(); }

        $name = $this->request->get('name');
        $paths = $this->request->get('paths', false);

        return cmsTemplate::getInstance()->render('upload_single', array(
           'name' => $name,
           'paths' => $paths
        ));

    }

    public function actionGetMultiUploadWidget(){

        if (!$this->request->isInternal()) { cmsCore::error404(); }

        $name = $this->request->get('name');
        $images = $this->request->get('images', false);

        return cmsTemplate::getInstance()->render('upload_multi', array(
           'name' => $name,
           'images' => $images
        ));

    }

//============================================================================//
//============================================================================//

    public function actionUpload($name){

        $config = cmsConfig::getInstance();

        $uploader = new cmsUploader();

        $result = $uploader->upload($name);

        if ($result['success']){
            if (!$uploader->isImage($result['path'])){
                $uploader->remove($result['path']);
                $result['success'] = false;
                $result['error'] = LANG_UPLOAD_ERR_MIME;
            }
        }
        
        if (!$result['success']){
            cmsTemplate::getInstance()->renderJSON($result);
            $this->halt();
        }

        $result['paths'] = array();

        $result['paths']['original']['path'] = $result['url'];
        $result['paths']['original']['url'] = $config->upload_host . '/' . $result['paths']['original']['path'];

        $result['paths']['big']['path'] = $uploader->resizeImage($result['path'], array('width'=>700, 'height'=>500, 'square'=>false));
        $result['paths']['big']['url'] = $config->upload_host . '/' . $result['paths']['big']['path'];

        $result['paths']['normal']['path'] = $uploader->resizeImage($result['path'], array('width'=>250, 'height'=>250, 'square'=>false));
        $result['paths']['normal']['url'] = $config->upload_host . '/' . $result['paths']['normal']['path'];

        $result['paths']['small']['path'] = $uploader->resizeImage($result['path'], array('width'=>64, 'height'=>64, 'square'=>true));
        $result['paths']['small']['url'] = $config->upload_host . '/' . $result['paths']['small']['path'];

        $result['paths']['micro']['path'] = $uploader->resizeImage($result['path'], array('width'=>32, 'height'=>32, 'square'=>true));
        $result['paths']['micro']['url'] = $config->upload_host . '/' . $result['paths']['micro']['path'];

        unset($result['path']);

        cmsTemplate::getInstance()->renderJSON($result);
        $this->halt();

    }

//============================================================================//
//============================================================================//

}
