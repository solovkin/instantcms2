<?php

class actionContentCategoryEdit extends cmsAction {

    public function run(){

        // Получаем название типа контента и сам тип
        $ctype_name = $this->request->get('ctype_name');
        $ctype = $this->model->getContentTypeByName($ctype_name);
        if (!$ctype) { cmsCore::error404(); }

        // проверяем поддержку категорий
        if (!$ctype['is_cats']){ cmsCore::error404(); }

        // проверяем наличие доступа
        if (!cmsUser::isAllowed($ctype['name'], 'edit_cat')) { cmsCore::error404(); }

        $id = $this->request->get('id');
        if (!$id) { cmsCore::error404(); }

        $form = $this->getForm('category');

        $category = $this->model->getCategory($ctype['name'], $id);

        // Форма отправлена?
        $is_submitted = $this->request->has('submit');

        if ($is_submitted){

            // Парсим форму и получаем поля записи
            $category = array_merge($category, $form->parse($this->request, $is_submitted));

            // Проверям правильность заполнения
            $errors = $form->validate($this,  $category);

            if (!$errors){
                // Добавляем запись и редиректим на ее просмотр
                $category = $this->model->updateCategory($ctype_name, $id, $category);
                $this->redirectTo($ctype_name, $category['slug']);

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return cmsTemplate::getInstance()->render('category_form', array(
            'do' => 'edit',
            'ctype' => $ctype,
            'category' => $category,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

}
