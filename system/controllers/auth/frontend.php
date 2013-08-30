<?php
class auth extends cmsFrontend {

    protected $useOptions = true;

//============================================================================//
//============================================================================//

	public function actionIndex(){

        $this->runAction('login');

  	}

//============================================================================//
//============================================================================//

    public function actionLogout(){

        cmsEventsManager::hook('auth_logout', cmsUser::getInstance()->id);

        cmsUser::logout();

        $this->redirectToHome();
        $this->halt();

    }

//============================================================================//
//============================================================================//

    public function actionOpenid(){

        if (cmsUser::isLogged()) { $this->redirectToHome(); }

        return cmsTemplate::getInstance()->render('openid', array());

    }


//============================================================================//
//============================================================================//

    public function actionNewpass($pass_token){

        $core       = cmsCore::getInstance();

        //проверяем токен
        if (!preg_match("/^([a-zA-Z0-9]{32})$/i", $pass_token)){ $this->redirectToHome(); }

        //ищем юзера
        $user_data = $core->db->getFields('{users}', "pass_token = '{$pass_token}'", 'id, unix_timestamp(pass_token_date) as ut');
        if (!$user_data) { $this->redirectToHome(); }

        $user_id = $user_data['id'];

        //проверяем свежесть токена
        $token_time = $user_data['ut'];
        if (time() - $token_time > 48*3600) {

            //если токен старше 48-ми часом, обнуляем его и не пускаем
            $sql = "UPDATE {#}{users}
                    SET pass_token_date = NULL, pass_token = NULL
                    WHERE id = '{$user_id}'";
            $core->db->query($sql);
            $this->redirectToHome();

        }

        //если это не сабмит формы
        if(!$this->inRequest('submit')){

			//Выводим форму
			return cmsTemplate::getInstance()->render('newpass', array());

		}

        //если сабмит формы
        if($this->inRequest('submit')){

            $new_pass   = $this->request->get('new_pass');
            $new_pass2  = $this->request->get('new_pass2');

            if (!$new_pass || !$new_pass2){
                cmsUser::addSessionMessage(ERR_NEW_PASS_REQUIRED, 'error');
                $this->redirectBack();
                $this->halt();
            }

			if($new_pass != $new_pass2) {
                cmsUser::addSessionMessage(ERR_NEW_PASS_MISMATCH, 'error');
                $this->redirectBack();
                $this->halt();
            }

            $pass_md5 = md5($new_pass);

            $sql = "UPDATE {#}{users}
                    SET password='{$pass_md5}', pass_token = NULL
                    WHERE id = '{$user_id}'";
            $core->db->query($sql);

            cmsUser::addSessionMessage(LANG_PASS_CHANGED, 'success');

            $this->redirectToAction('login');

		}

    }

//============================================================================//
//============================================================================//

	private function sendRegistration($email, $password=''){

        $core   = cmsCore::getInstance();
		$config = cmsConfig::getInstance();

		$letter_file    = $config->root_path.'system/languages/'.$config->language.'/register.txt';
		$letter         = file_get_contents($letter_file);

        $subj           = LANG_REGISTRATION_MAIL;

        $mailer = $core->getMailer();

        $mailer->AddAddress($email);

        $mailer->Subject = $subj;
        $mailer->Body    = $letter;

        if ($mailer->Send()){
            return true;
        } else {
            return false;
        }

	}

	private function sendToken($email, $token){

        $core   = cmsCore::getInstance();
		$config = cmsConfig::getInstance();

		$letter_file    = $config->root_path.'system/languages/'.$config->language.'/pass_restore.txt';
		$letter         = file_get_contents($letter_file);

        $letter         = str_replace('{token}', $token, $letter);

        $subj           = LANG_PASS_RESTORE;

        $mailer = $core->getMailer();

        $mailer->AddAddress($email);

        $mailer->Subject = $subj;
        $mailer->Body    = $letter;

        if ($mailer->Send()){
            return true;
        } else {
            return false;
        }

	}

//============================================================================//
//============================================================================//

    public function actionLoginza(){

        $wid    = '4688';
        $skey   = '7d3c8dfe4b0a5fe9dcc2c003f0f892c7';

        $token  = $this->request->get('token');
        $sig    = md5($token.$skey);

        if (!$token){
            cmsUser::addSessionMessage(LANG_LOGIN_ERROR, 'error');
            $this->redirect('/auth/login');
        }

        $loginza_api_url = 'http://loginza.ru/api/authinfo';

        $back_url = cmsUser::sessionGet('auth_back_url') ? cmsUser::sessionGet('auth_back_url', true) : '/profile';

        $profile = $this->loginzaRequest($loginza_api_url."?token={$token}&id={$wid}&sig={$sig}");

        $profile = json_decode($profile);

        //
        // проверка на ошибки
        //
        if (!is_object($profile) || !empty($profile->error_message) || !empty($profile->error_type)) {
            cmsUser::addSessionMessage(LANG_LOGIN_ERROR, 'error');
            $this->redirect('/auth/login');
        }

        $identity   = $profile->identity;
        $email      = isset($profile->email) ? $profile->email : '';

        // ищем такого пользователя
        $user_id    = cmsUser::getUserByOpenID($identity);

        //
        // если пользователя нет, создаем
        //
        if (!$user_id){

            if ($profile->name->full_name){

                // указано полное имя
                $nickname   = $profile->name->full_name;

            } elseif($profile->name->first_name) {

                // указано имя и фамилия по-отдельности
                $nickname   = $profile->name->first_name;
                if ($profile->name->last_name){ $nickname .= ' '. $profile->name->last_name; }

            } elseif($profile->identity) {

                // не указано имя, но передан идентификатор в виде домена 3-го уровня
                $nickname   = str_replace('http://', '', trim($identity, '/ '));

            } else {

                // не указано вообще ничего
                $nickname = 'Anonymous';

            }

            $password   = substr(md5($email.time().session_id()), 0, 8);
            $user_id    = cmsUser::registerByOpenID($identity, $password, $nickname, $email);

            if ($user_id){
                if ($email){ $this->sendRegistration($email, $password); }
                cmsUser::loginByOpenID($identity, $user_id);
                cmsUser::addSessionMessage(LANG_REG_SUCCESS, 'success');
                $this->redirect($back_url);
            }

        }

        //
        // если пользователь уже был, авторизуем
        //
        if ($user_id){
            if (cmsUser::loginByOpenID($identity, $user_id)){
                $this->redirect($back_url);
            }
        }

        //
        // если авторизация не удалась, редиректим на сообщение об ошибке
        //
        cmsUser::addSessionMessage(LANG_LOGIN_ERROR, 'error');
        $this->redirect('/auth/login');

    }

//============================================================================//
//============================================================================//

    private function loginzaRequest($url) {

        if (function_exists('curl_init')){

            $curl = curl_init($url);
            $user_agent = 'Loginza-API/Simpoll';

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $raw_data = curl_exec($curl);
            curl_close($curl);

            return $raw_data;

        } else {

            return file_get_contents($url);

        }

    }

//============================================================================//
//============================================================================//

    public function isEmailAllowed($value){

        $list = $this->options['restricted_emails'];

        return !string_in_mask_list($value, $list);

    }

    public function isNameAllowed($value){

        $list = $this->options['restricted_names'];

        return !string_in_mask_list($value, $list);

    }

    public function isIPAllowed($value){

        $list = $this->options['restricted_ips'];

        return !string_in_mask_list($value, $list);

    }

//============================================================================//
//============================================================================//

}
