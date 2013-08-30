<?php if ($do=='add') { ?><h1><?php echo LANG_CP_CTYPES_ADD; ?></h1><?php } ?>
<?php if ($do=='edit') { ?><h1><?php echo LANG_CONTENT_TYPE; ?>: <span><?php echo $ctype['title']; ?></span></h1><?php } ?>

<?php

    $this->setPageTitle(LANG_CP_SECTION_CTYPES);

    $this->addBreadcrumb(LANG_CP_SECTION_CTYPES, $this->href_to('ctypes'));

    if ($do=='add'){
        $this->addBreadcrumb(LANG_CP_CTYPES_ADD);
        $this->addMenuItems('ctype', $this->controller->getCtypeMenu('add'));
    }

    if ($do=='edit'){
        $this->addBreadcrumb($ctype['title']);
        $this->addMenuItems('ctype', $this->controller->getCtypeMenu('edit', $id));
    }

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));
    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('ctypes')
    ));
    $this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_CTYPES_BASIC
	));
?>

<div class="pills-menu">
    <?php $this->menu('ctype'); ?>
</div>

<?php
    $this->renderForm($form, $ctype, array(
        'action' => '',
        'method' => 'post'
    ), $errors);
?>
