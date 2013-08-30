<?php

    $this->addJS('templates/default/js/jquery-ui.js');
    $this->addJS('templates/default/js/jquery-cookie.js');
    $this->addJS('templates/default/js/datatree.js');
    $this->addJS('templates/default/js/admin-widgets.js');
    $this->addCSS('templates/default/css/datatree.css');
    $this->addCSS('templates/default/css/jquery-ui.css');

    $this->setPageTitle(LANG_CP_SECTION_WIDGETS);
    $this->addBreadcrumb(LANG_CP_SECTION_WIDGETS, $this->href_to('widgets'));

    $this->addToolButton(array(
        'class' => 'add',
        'title' => LANG_CP_WIDGETS_ADD_PAGE,
        'href'  => $this->href_to('widgets', 'page_add')
    ));
    $this->addToolButton(array(
        'class' => 'edit',
        'title' => LANG_CP_WIDGETS_EDIT_PAGE,
        'href'  => $this->href_to('widgets', 'page_edit')
    ));
    $this->addToolButton(array(
        'class' => 'delete',
        'title' => LANG_CP_WIDGETS_DELETE_PAGE,
        'href'  => $this->href_to('widgets', 'page_delete')
    ));
	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_WIDGETS
	));

?>

<h1><?php echo LANG_CP_SECTION_WIDGETS; ?></h1>

<table class="layout">
    <tr>
        <td class="sidebar" valign="top">

            <div id="datatree">
                <ul id="treeData" style="display: none">
                    <li id="core" class="folder">
                        <?php echo LANG_WP_SYSTEM; ?>
                        <ul>
                            <li id="core.0"><?php echo LANG_WP_ALL_PAGES; ?></li>
                            <li id="core.1"><?php echo LANG_WP_HOME_PAGE; ?></li>
                        </ul>
                    </li>
                    <?php foreach($controllers as $controller_name => $controller_title){ ?>
                        <li id="<?php echo $controller_name ? $controller_name : 'custom'; ?>" class="lazy folder"><?php echo $controller_title; ?></li>
                    <?php } ?>
                </ul>
            </div>

        </td>
        <td class="main" valign="top" style="padding-right:10px">

            <div class="cp_toolbar">
                <?php $this->toolbar(); ?>
            </div>

            <div id="cp-widgets-layout"
                 data-tree-url="<?php echo $this->href_to('widgets', 'tree_ajax'); ?>"
                 data-load-url="<?php echo $this->href_to('widgets', 'load'); ?>"
                 data-add-url="<?php echo $this->href_to('widgets', 'add'); ?>"
                 data-edit-url="<?php echo $this->href_to('widgets', 'edit'); ?>"
                 data-delete-url="<?php echo $this->href_to('widgets', 'delete'); ?>"
                 data-edit-page-url="<?php echo $this->href_to('widgets', 'page_edit'); ?>"
                 data-delete-page-url="<?php echo $this->href_to('widgets', 'page_delete'); ?>"
                 data-reorder-url="<?php echo $this->href_to('widgets', 'reorder'); ?>"
                 >
                <?php echo $scheme_html; ?>
            </div>

        </td>
        <td class="sidebar" valign="top" width="150">

            <div id="cp-widgets-list">

                <?php if ($widgets_list){ ?>

                    <div id="accordion">

                        <?php foreach($widgets_list as $controller_name=>$widgets){ ?>

                            <div class="section">

                                <?php $controller_title = $controller_name ? constant("LANG_".mb_strtoupper($controller_name)."_CONTROLLER") : LANG_CP_WIDGETS_MISC; ?>

                                <a class="section-open" href="#" rel="<?php echo $controller_name; ?>"><span>&rarr;</span> <?php echo $controller_title; ?></a>
                                <ul>
                                    <?php foreach($widgets as $widget){ ?>
                                        <li rel="new" data-id="<?php echo $widget['id']; ?>">
                                            <?php echo $widget['title']; ?>
                                        </li>
                                    <?php } ?>
                                </ul>

                            </div>

                        <?php } ?>

                    </div>

                <?php } ?>

                <div id="actions-template" style="display:none">
                    <span class="actions">
                        <a class="edit" href="javascript:" title="<?php echo LANG_EDIT; ?>"></a>
                        <a class="delete" href="javascript:" title="<?php echo LANG_DELETE; ?>"></a>
                    </span>
                </div>

            </div>

        </td>
    </tr>
</table>

<script>

    <?php echo $this->getLangJS('LANG_CP_WIDGET_DELETE_CONFIRM'); ?>

</script>
