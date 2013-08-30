<?php

class frontpage extends cmsFrontend {

	public function actionIndex(){

        $mode = cmsConfig::get('frontpage');

        //
        // Только виджеты
        //
        if (!$mode || $mode == 'none') {

            return false;

        }

        //
        // Профиль / авторизация
        //
        if ($mode == 'profile'){

            $user = cmsUser::getInstance();

            if ($user->is_logged){ $this->redirectTo('users', $user->id); }

            $auth_controller = cmsCore::getController('auth', new cmsRequest(array(
                'is_frontpage' => true
            )));

            return $auth_controller->runAction('login');

        }

        //
        // Контент
        //
        if (mb_strstr($mode, 'content:')){

            list($mode, $ctype_name) = explode(':', $mode);

            $request = new cmsRequest(array(
                'ctype_name' => $ctype_name,
                'slug' => 'index',
                'is_frontpage' => true
            ));

            $content_controller = cmsCore::getController('content', $request);

            return $content_controller->runAction('item_category');

        }

	}

}
