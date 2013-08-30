<?php

class actionMessagesSend extends cmsAction {

    public function run(){

        if (!$this->request->isAjax()){ cmsCore::error404(); }

        $template = cmsTemplate::getInstance();
        $config = cmsConfig::getInstance();
        $user = cmsUser::getInstance();

        $contact_id = $this->request->get('contact_id') or cmsCore::error404();
        $content = $this->request->get('content') or cmsCore::error404();
        $csrf_token = $this->request->get('csrf_token');

        // Проверяем валидность
        $is_valid = $this->validate_number($contact_id) &&
                    cmsForm::validateCSRFToken($csrf_token, false);

        if (!$is_valid){
            $result = array('error' => true, 'message' => '');
            $template->renderJSON($result);
        }

        $contact = $this->model->getContact($user->id, $contact_id);

        // Контакт существует?
        if (!$contact){
            $result = array('error' => true, 'message' => '');
            $template->renderJSON($result);
        }

        // Контакт не в игноре у отправителя?
        if ($contact['is_ignored']){
            $result = array('error' => true, 'message' => LANG_PM_CONTACT_IS_IGNORED);
            $template->renderJSON($result);
        }

        // Отправитель не в игноре у контакта?
        if ($this->model->isContactIgnored($contact_id, $user->id)){
            $result = array('error' => true, 'message' => LANG_PM_YOU_ARE_IGNORED);
            $template->renderJSON($result);
        }

        // Контакт принимает сообщения от этого пользователя?
        if (!$user->isPrivacyAllowed($contact, 'messages_pm')){
            $result = array('error' => true, 'message' => LANG_PM_CONTACT_IS_PRIVATE);
            $template->renderJSON($result);
        }

        //
        // Отправляем сообщение
        //
        $this->setSender($user->id);
        $this->addRecipient($contact_id);
        $message_id = $this->sendMessage($content);

        //
        // Получаем и рендерим добавленное сообщение
        //
        $message = $this->model->getMessage($message_id);
        $message_html = $template->render('message', array(
            'messages' => array($message),
            'user'=>$user
        ), new cmsRequest(array(), cmsRequest::CTX_INTERNAL));

        // Результат
        $template->renderJSON(array(
            'error' => false,
            'date'  => date($config->date_format, time()),
            'message' => $message_html
        ));

    }

}
