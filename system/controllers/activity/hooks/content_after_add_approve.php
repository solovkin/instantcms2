<?php

class onActivityContentAfterAddApprove extends cmsAction {

    public function run($data){

        $ctype_name = $data['ctype_name'];
        $item = $data['item'];

        $this->addEntry('content', "add.{$ctype_name}", array(
            'user_id' => $item['user_id'],
            'subject_title' => $item['title'],
            'subject_id' => $item['id'],
            'subject_url' => href_to($ctype_name, $item['slug'] . '.html'),
            'is_private' => isset($item['is_private']) ? $item['is_private'] : 0,
            'group_id' => isset($item['parent_id']) ? $item['parent_id'] : null
        ));

        return $data;

    }

}
