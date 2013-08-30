<?php

class actionContentCategoryAdd extends cmsAction {

    public function run(){

        // Получаем название типа контента и сам тип
        $ctype_name = $this->request->get('ctype_name');
        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) { cmsCore::error404(); }

        // проверяем поддержку категорий
        if (!$ctype['is_cats']){ cmsCore::error404(); }

        // проверяем наличие доступа
        if (!cmsUser::isAllowed($ctype['name'], 'add_cat')) { cmsCore::error404(); }

        $parent_id = $this->request->get('to_id');

        $form = $this->getForm('category');

        // Форма отправлена?
        $is_submitted = $this->request->has('submit');

        // Парсим форму и получаем поля записи
        $category = $form->parse($this->request, $is_submitted);

        if (!$is_submitted && $parent_id) { $category['parent_id'] = $parent_id; }

        if ($is_submitted){

            // Проверям правильность заполнения
            $errors = $form->validate($this,  $category);

            if (!$errors){
                // Добавляем запись и редиректим на ее просмотр
                $category = $this->model->addCategory($ctype_name, $category);
                $this->redirectTo($ctype_name, $category['slug']);
            }

            if ($errors){
            cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return cmsTemplate::getInstance()->render('category_form', array(
            'do' => 'add',
            'ctype' => $ctype,
            'category' => $category,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
