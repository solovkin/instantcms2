<?php
class markitup extends cmsFrontend {

    protected $useOptions = true;

//============================================================================//
//============================================================================//

    public function getEditorWidget($field_id, $content='', $options=array()){

        if ($this->request->isInternal()){
            if ($this->useOptions){
               $this->options = $this->getOptions();
            }
        }

        $this->options['id'] = 'editor'.time().'_'.$field_id;

        $options = array_merge($this->options, $options);

        $template = cmsTemplate::getInstance();

        return $template->renderInternal($this, 'widget', array(
            'field_id' => $field_id,
            'content' => $content,
            'options' => $options
        ));

    }

//============================================================================//
//============================================================================//

    public function actionUpload(){

        $config = cmsConfig::getInstance();

        $uploader = new cmsUploader();

        $result = $uploader->upload('inline_upload_file', 'gif,jpg,jpeg,png');

        if (!$result['success']){

            cmsTemplate::getInstance()->renderJSON(array(
                'status' => 'error',
                'msg' => $result['error']
            ));

            $this->halt();

        }

        $path = $uploader->resizeImage($result['path'], array('width'=>400, 'height'=>400, 'square'=>false));
        $src = $config->upload_host . '/' . $path;

        unset($result['path']);

        cmsTemplate::getInstance()->renderJSON(array(
            'status' => 'success',
            'src' => $src
        ));

        $this->halt();

    }

//============================================================================//
//============================================================================//

}
