<?php

    $this->setPageTitle($ctype['title'], $profile['nickname']);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($profile['nickname'], $this->href_to($profile['id']));
    $this->addBreadcrumb($ctype['title']);

    $content_menu = array();

    foreach($this->controller->content_counts as $ctype_name=>$count){
        if (!$count['is_in_list']) { continue; }
        $content_menu[] = array(
            'title' => $count['title'],
            'url' => href_to('users', $profile['id'], array('content', $ctype_name)),
            'counter' => $count['count']
        );
    }

    $this->addMenuItems('profile_content_types', $content_menu);

    if (cmsUser::isAllowed($ctype['name'], 'add')) {

        $is_allowed = true;

        if ($is_allowed){

            $href = href_to($ctype['name'], 'add');

            $this->addToolButton(array(
                'class' => 'add',
                'title' => sprintf(LANG_CONTENT_ADD_ITEM, $ctype['labels']['create']),
                'href'  => $href,
            ));

        }

    }

    if (cmsUser::isAdmin()){
        $this->addToolButton(array(
            'class' => 'page_gear',
            'title' => sprintf(LANG_CONTENT_TYPE_SETTINGS, mb_strtolower($ctype['title'])),
            'href'  => href_to('admin', 'ctypes', array('edit', $ctype['id']))
        ));
    }

?>

<div id="user_content_pills">
    <?php $this->menu('profile_content_types', true, 'pills-menu-small'); ?>
</div>

<div id="user_content_list"><?php echo $html; ?></div>
