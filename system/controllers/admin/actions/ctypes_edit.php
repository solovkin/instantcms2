<?php

class actionAdminCtypesEdit extends cmsAction {

    public function run($id){

        if (!$id) { cmsCore::error404(); }

        $content_model = cmsCore::getModel('content');

        $form = $this->getForm('ctypes_basic', array('edit'));

        $form->hideField('titles', 'name');

        $is_submitted = $this->request->has('submit');

        $ctype = $content_model->getContentType($id);
        
        if (!$ctype) { cmsCore::error404(); }

        if ($is_submitted){

            $ctype = $form->parse($this->request, $is_submitted);
            $errors = $form->validate($this,  $ctype);

            if (!$errors){

                $content_model->updateContentType($id, $ctype);

                $this->redirectToAction('ctypes');

            }

            if ($errors){

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        return cmsTemplate::getInstance()->render('ctypes_basic', array(
            'id' => $id,
            'do' => 'edit',
            'ctype' => $ctype,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
