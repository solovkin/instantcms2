<?php

class actionContentItemCategory extends cmsAction {

    public function run(){

        $user = cmsUser::getInstance();

        // Получаем название типа контента и сам тип
        $ctype_name = $this->request->get('ctype_name');
        $ctype = $this->model->getContentTypeByName($ctype_name);

        if (!$ctype) { cmsCore::error404(); }
        if (!$ctype['options']['list_on']) { cmsCore::error404(); }

        $category = array('id' => false);
        $subcats = array();

        // Получаем SLUG категории
        $slug = $this->request->get('slug');

        if (!$ctype['is_cats'] && $slug != 'index') { cmsCore::error404(); }

        if ($ctype['is_cats'] && $slug != 'index') {
            $category = $this->model->getCategoryBySLUG($ctype_name, $slug);
            if (!$category){ cmsCore::error404(); }
        }

        // Получаем список подкатегорий для текущей
        $current_cat_id = $category['id'] ? $category['id'] : 1;
        $subcats = $this->model->getSubCategories($ctype_name, $current_cat_id);

        // Получаем список наборов
        $datasets = $this->model->getContentDatasets($ctype['id'], true);

        // Текущий набор
        $dataset = $this->request->get('dataset', false);

        // Это вывод на главной?
        $is_frontpage = $this->request->get('is_frontpage', false);

        // Номер страницы
        $page = $this->request->get('page', 1);

        // Если это не главная, но данный контент выводится на главной и сейчас
        // открыта индексная страница контента - редиректим на главную
        if (!$is_frontpage && cmsConfig::get('frontpage') == "content:{$ctype_name}" && $slug == 'index' && !$dataset && $page==1){
            $this->redirectToHome();
        }

        // Если есть наборы, применяем фильтры текущего
        // иначе будем сортировать по дате создания
        if ($datasets){
            if($dataset && empty($datasets[$dataset])){ cmsCore::error404(); }
            $keys = array_keys($datasets);
            $current_dataset = $dataset ? $datasets[$dataset] : $datasets[$keys[0]];
            $this->model->applyDatasetFilters($current_dataset);
        } else {
            $this->model->orderBy('date_pub', 'desc');
        }

        // Фильтр по категории
        if ($ctype['is_cats'] && $slug != 'index') {
            $this->model->filterCategory($ctype_name, $category, $ctype['is_cats_recursive']);
        }

        // Скрываем записи из скрытых родителей (приватных групп и т.п.)
        $this->model->filterHiddenParents();

        // Формируем базовые URL для страниц
        $page_url = array(
            'base'  => href_to($ctype['name'] . ($dataset ? '-'.$dataset : ''), isset($category['slug']) ? $category['slug'] : ''),
            'first' => href_to($ctype['name'] . ($dataset ? '-'.$dataset : ''), isset($category['slug']) ? $category['slug'] : '')
        );

        // Получаем HTML списка записей
        $items_list_html = $this->renderItemsList($ctype, $page_url);

        return cmsTemplate::getInstance()->render('item_category', array(
            'is_frontpage' => $is_frontpage,
            'parent' => isset($parent) ? $parent : false,
            'slug' => $slug,
            'ctype' => $ctype,
            'datasets' => $datasets,
            'dataset' => $dataset,
            'category' => $category,
            'subcats' => $subcats,
            'items_list_html' => $items_list_html,
            'user' => $user
        ), $this->request);

    }

}
