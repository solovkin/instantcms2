<?php

    $page_title =   $do=='add' ?
                    sprintf(LANG_CONTENT_ADD_ITEM, $ctype['labels']['create']) :
                    $item['title'];

    if ($ctype['options']['list_on'] && !$parent){
        $this->addBreadcrumb($ctype['title'], href_to($ctype['name']));
    }

    if ($parent){

        if ($parent['ctype']['options']['list_on']){
            $this->addBreadcrumb($parent['ctype']['title'], href_to($parent['ctype']['name']));
        }

        $this->addBreadcrumb($parent['item']['title'], href_to($parent['ctype']['name'], $parent['item']['slug'].'.html'));

    }

    $back_url = $this->controller->request->get('back');

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));

    if ($ctype['options']['list_on']){
        $this->addToolButton(array(
            'class' => 'cancel',
            'title' => LANG_CANCEL,
            'href'  => $back_url ? $back_url : href_to($ctype['name'])
        ));
    }

    $this->addBreadcrumb($page_title);

?>

<h1><?php echo $page_title ?></h1>

<?php
    $item['ctype_name'] = $ctype['name'];

    $this->renderForm($form, $item, array(
        'action' => '',
        'method' => 'post',
        'toolbar' => false,
        'hook' => array(
            'event' => "content_{$ctype['name']}_form_html",
            'param' => array(
                'do' => $do,
                'id' => $do=='edit' ? $item['id'] : null
            )
        ),
    ), $errors);
?>

<?php if ($is_premoderation && !$is_moderator) { ?>
<div class="content_moderation_notice icon-info">
    <?php echo LANG_MODERATION_NOTICE; ?>
</div>
<?php } ?>
