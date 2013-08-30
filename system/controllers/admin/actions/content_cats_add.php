<?php

class actionAdminContentCatsAdd extends cmsAction {

    public function run($ctype_id, $parent_id=1){

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);

        $form = $this->getForm('content_category');

        // Форма отправлена?
        $is_submitted = $this->request->has('submit');

        // Парсим форму и получаем поля записи
        $category = $form->parse($this->request, $is_submitted);

        if (!$is_submitted && $parent_id) { $category['parent_id'] = $parent_id; }

        if ($is_submitted){

            // Проверям правильность заполнения
            $errors = $form->validate($this,  $category);

            if (!$errors){

                $this->createCategories($content_model, $ctype, $category);

                $this->redirectToAction('content');

            }

            if ($errors){
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }

        }

        return cmsTemplate::getInstance()->render('content_cats_add', array(
            'ctype' => $ctype,
            'category' => $category,
            'form' => $form,
            'errors' => isset($errors) ? $errors : false
        ));

    }

    private function createCategories($content_model, $ctype, $data){

        $list = explode("\n", $data['title']);

        if (count($list) == 1){
            $content_model->addCategory($ctype['name'], $data);
            return;
        }

        $levels_ids = array();

        foreach($list as $category_title){

            $category_title = trim($category_title);

            $is_sub = strpos($category_title, '-')===0;

            if (!$is_sub){

                $levels_ids = array();

                $result = $content_model->addCategory($ctype['name'], array(
                    'parent_id' => $data['parent_id'],
                    'title' => $category_title
                ));

                $levels_ids[0] = $result['id'];

                continue;

            }

            $level = mb_strlen(str_replace(' ', '', $category_title)) - mb_strlen(ltrim(str_replace(' ', '', $category_title), '-'));
            $parent_id = $levels_ids[$level - 1];

            $result = $content_model->addCategory($ctype['name'], array(
                'parent_id' => $parent_id,
                'title' => ltrim($category_title, '- ')
            ));

            $levels_ids[$level] = $result['id'];

        }

    }

}
