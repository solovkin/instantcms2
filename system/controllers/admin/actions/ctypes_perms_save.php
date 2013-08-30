<?php

class actionAdminCtypesPermsSave extends cmsAction {

    public function run($ctype_name){

        $values = $this->request->get('value');

        if (!$values || !$ctype_name) { cmsCore::error404(); }

        $rules = cmsPermissions::getRulesList('content');

        $users_model = cmsCore::getModel('users');
        $groups = $users_model->getGroups(false);

        // перебираем правила
        foreach($rules as $rule){

            // если для этого правила вообще ничего нет,
            // то присваиваем null
            if (empty($values[$rule['id']])) {
                $values[$rule['id']] = null; continue;
            }

            // перебираем группы, заменяем на нуллы
            // значения отсутствующих правил
            foreach($groups as $group){
                if (empty($values[$rule['id']][$group['id']])) {
                    $values[$rule['id']][$group['id']] = null;
                }
            }

        }

        cmsPermissions::savePermissions($ctype_name, $values);

        $this->redirectBack();

    }

}
