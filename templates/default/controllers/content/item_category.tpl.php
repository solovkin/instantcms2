<?php

    $page_header = isset($category['title']) ? $category['title'] : $ctype['title'];
    $base_url = $ctype['name'];
    $base_ds_url = $ctype['name'] . '-%s' . (isset($category['slug']) ? '/'.$category['slug'] : '');

    if (!$is_frontpage){
        $this->setPageTitle($page_header);
    }

    if ($ctype['options']['list_on'] && !$request->isInternal() && !$is_frontpage){
        $this->addBreadcrumb($ctype['title'], href_to($base_url));
    }

    if (isset($category['path']) && $category['path']){
        foreach($category['path'] as $c){
            $this->addBreadcrumb($c['title'], href_to($base_url, $c['slug']));
        }
    }

    if (cmsUser::isAllowed($ctype['name'], 'add')) {

        $is_allowed = true;

        if ($is_allowed){

            $href = href_to($ctype['name'], 'add', isset($category['path']) ? $category['id'] : '');

            $this->addToolButton(array(
                'class' => 'add',
                'title' => sprintf(LANG_CONTENT_ADD_ITEM, $ctype['labels']['create']),
                'href'  => $href
            ));

        }

    }

    if ($ctype['is_cats']){
        if (cmsUser::isAllowed($ctype['name'], 'add_cat')) {
            $this->addToolButton(array(
                'class' => 'folder_add',
                'title' => LANG_ADD_CATEGORY,
                'href'  => href_to($ctype['name'], 'addcat', $category['id'])
            ));
        }

        if ($category['id']){

            if (cmsUser::isAllowed($ctype['name'], 'edit_cat')) {
                $this->addToolButton(array(
                    'class' => 'folder_edit',
                    'title' => LANG_EDIT_CATEGORY,
                    'href'  => href_to($ctype['name'], 'editcat', $category['id'])
                ));
            }
            if (cmsUser::isAllowed($ctype['name'], 'delete_cat')) {
                $this->addToolButton(array(
                    'class' => 'folder_delete',
                    'title' => LANG_DELETE_CATEGORY,
                    'href'  => href_to($ctype['name'], 'delcat', $category['id']),
                    'onclick' => "if(!confirm('".LANG_DELETE_CATEGORY_CONFIRM."')){ return false; }"
                ));
            }

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

<?php if ($page_header && !$request->isInternal()){  ?>
    <h1><?php echo $page_header; ?></h1>
<?php } ?>

<?php if ($datasets){ ?>
    <div class="content_datasets">
        <ul class="pills-menu">
            <?php $ds_counter = 0; ?>
            <?php foreach($datasets as $set){ ?>
                <?php $ds_selected = ($dataset == $set['name'] || (!$dataset && $ds_counter==0)); ?>
                <li <?php if ($ds_selected){ ?>class="active"<?php } ?>>

                    <?php if ($ds_counter > 0) { $ds_url = sprintf(href_to($base_ds_url), $set['name']); } ?>
                    <?php if ($ds_counter == 0) { $ds_url = href_to($base_url, isset($category['slug']) ? $category['slug'] : ''); } ?>

                    <?php if ($ds_selected){ ?>
                        <div><?php echo $set['title']; ?></div>
                    <?php } else { ?>
                        <a href="<?php echo $ds_url; ?>"><?php echo $set['title']; ?></a>
                    <?php } ?>

                </li>
                <?php $ds_counter++; ?>
            <?php } ?>
        </ul>
    </div>
<?php } ?>

<?php if ($subcats && $ctype['is_cats']){ ?>
    <div class="content_categories">
        <ul>
            <?php foreach($subcats as $c){ ?>
                <li>
                    <a href="<?php echo href_to($base_url . ($dataset ? '-'.$dataset : ''), $c['slug']); ?>"><?php echo $c['title']; ?></a>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>

<?php echo $items_list_html; ?>
