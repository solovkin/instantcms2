<?php

class actionAdminContentFilter extends cmsAction {

    public function run($ctype_id){

        $content_model = cmsCore::getModel('content');

        $ctype = $content_model->getContentType($ctype_id);

        $datasets = $content_model->getContentDatasets($ctype_id);

        $fields  = $content_model->getContentFields($ctype['name']);

        $fields[] = array(
            'title' => LANG_RATING,
            'name' => 'rating',
            'handler' => new fieldNumber('rating')
        );

        $fields[] = array(
            'title' => LANG_COMMENTS,
            'name' => 'comments',
            'handler' => new fieldNumber('comments')
        );

        return cmsTemplate::getInstance()->render('content_filter', array(
            'ctype' => $ctype,
            'datasets' => $datasets,
            'fields' => $fields
        ));

    }

}
