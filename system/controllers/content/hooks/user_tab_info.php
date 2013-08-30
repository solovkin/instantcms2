<?php

class onContentUserTabInfo extends cmsAction {

    public function run($profile, $tab_name){

        $total = 0;

        $first_ctype_name = false;
        $this->content_counts = $this->model->getUserContentCounts($profile['id']);

        if ($this->content_counts){
            foreach($this->content_counts as $ctype_name=>$count){
                if (!$count['is_in_list']) { continue; }
                if (!$first_ctype_name) { $first_ctype_name = $ctype_name; }
                $total += $count['count'];
            }
        }

        if (!$total) { return false; }

        return array(
            'counter' => $total,
            'url' => href_to('users', $profile['id'], array($this->name, $first_ctype_name)),
            'url_mask' => href_to('users', $profile['id'], $this->name)
        );

    }

}
