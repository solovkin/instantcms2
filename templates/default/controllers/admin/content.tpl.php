<?php
    $this->addCSS('templates/default/css/datatree.css');
    $this->addJS('templates/default/js/jquery-ui.js');
    $this->addJS('templates/default/js/jquery-cookie.js');
    $this->addJS('templates/default/js/datatree.js');
    $this->addJS('templates/default/js/admin-content.js');
?>

<?php
    $this->setPageTitle(LANG_CP_SECTION_CONTENT);

    $this->addBreadcrumb(LANG_CP_SECTION_CONTENT, $this->href_to('content'));

    $this->addToolButton(array(
        'class' => 'filter',
        'title' => LANG_FILTER,
        'href'  => null,
        'onclick' => "return icms.modal.openAjax($(this).attr('href'))"
    ));

    $this->addToolButton(array(
        'class' => 'delete_filter important',
        'title' => LANG_CANCEL,
        'href'  => null,
        'onclick' => "return contentCancelFilter()"
    ));

    $this->addToolButton(array(
        'class' => 'settings',
        'title' => LANG_CONFIG,
        'href'  => $this->href_to('ctypes', array('edit'))
    ));

    $this->addToolButton(array(
        'class' => 'add_folder',
        'title' => LANG_CP_CONTENT_CATS_ADD,
        'href'  => $this->href_to('content', array('cats_add'))
    ));

    $this->addToolButton(array(
        'class' => 'delete_folder',
        'title' => LANG_DELETE_CATEGORY,
        'href'  => $this->href_to('content', array('cats_delete')),
        'confirm' => LANG_DELETE_CATEGORY_CONFIRM
    ));

    $this->addToolButton(array(
        'class' => 'tree_folder',
        'title' => LANG_CP_CONTENT_CATS_ORDER,
        'href'  => $this->href_to('content', array('cats_order')),
        'onclick' => 'return contentCatsReorder($(this))'
    ));

    $this->addToolButton(array(
        'class' => 'add',
        'title' => LANG_CP_CONTENT_ITEM_ADD,
        'href'  => $this->href_to('content', array('item_add'))
    ));

    $this->addToolButton(array(
        'class' => 'move',
        'title' => LANG_MOVE,
        'href'  => null,
        'onclick' => 'return icms.datagrid.submitAjax($(this))'
    ));

    $this->addToolButton(array(
        'class' => 'delete',
        'title' => LANG_DELETE,
        'href'  => null,
        'onclick' => "return icms.datagrid.submit($(this), '".LANG_DELETE_SELECTED_CONFIRM."')",
    ));

    $this->addToolButton(array(
        'class' => 'help',
        'title' => LANG_HELP,
        'target' => '_blank', 
        'href'  => LANG_HELP_URL_CONTENT,
    ));

?>

<h1><?php echo LANG_CP_SECTION_CONTENT; ?></h1>

<table class="layout">
    <tr>
        <td class="sidebar" valign="top">

            <div id="datatree">
                <ul id="treeData" style="display: none">
                    <?php foreach($ctypes as $id=>$ctype){ ?>
                        <li id="<?php echo $ctype['id'];?>.1" class="lazy folder"><?php echo $ctype['title']; ?></li>
                    <?php } ?>
                </ul>
            </div>

            <script type="text/javascript">
                    $(function(){

                        $('.cp_toolbar .delete_filter a').hide();
                        current_ctype = 0;

                        $("#datatree").dynatree({

                            onPostInit: function(isReloading, isError){
                                var path = $.cookie('icms[content_tree_path]');
                                if (!path) { path = '1.1'; }
                                if (path) {
                                    $("#datatree").dynatree("getTree").loadKeyPath(path, function(node, status){
                                        if(status == "loaded") {
                                            node.expand();
                                        }else if(status == "ok") {
                                            node.activate();
                                            node.expand();
                                            icms.datagrid.init();
                                        }
                                    });
                                }
                            },

                            onActivate: function(node){
                                node.expand();
                                $.cookie('icms[content_tree_path]', node.getKeyPath(), {expires: 7, path: '/'});
                                var key = node.data.key.split('.');
                                icms.datagrid.setURL("<?php echo $this->href_to('content', array('items_ajax')); ?>/" + key[0] + "/" + key[1]);
                                $('.cp_toolbar .filter a').attr('href', "<?php echo $this->href_to('content', array('filter')); ?>/" + key[0]);
                                $('.cp_toolbar .settings a').attr('href', "<?php echo $this->href_to('ctypes', array('edit')); ?>/" + key[0]);
                                $('.cp_toolbar .add a').attr('href', "<?php echo $this->href_to('content', array('item_add')); ?>/" + key[0] + "/" + key[1]);
                                $('.cp_toolbar .add_folder a').attr('href', "<?php echo $this->href_to('content', array('cats_add')); ?>/" + key[0] + "/" + key[1]);
                                $('.cp_toolbar .delete_folder a').attr('href', "<?php echo $this->href_to('content', array('cats_delete')); ?>/" + key[0] + "/" + key[1]);
                                $('.cp_toolbar .tree_folder a').attr('href', "<?php echo $this->href_to('content', array('cats_order')); ?>/" + key[0]);
                                $('.cp_toolbar .move a').data('url', "<?php echo $this->href_to('content', array('item_move')); ?>/" + key[0] + "/" + key[1]);
                                $('.cp_toolbar .delete a').data('url', "<?php echo $this->href_to('content', array('item_delete')); ?>/" + key[0]);
                                if (key[1] == 1){
                                    $('.cp_toolbar .delete_folder a').hide();
                                } else {
                                    $('.cp_toolbar .delete_folder a').show();
                                }
                                if (key[0] != current_ctype){ contentCancelFilter(); }
                                current_ctype = key[0];
                                icms.datagrid.loadRows();
                            },

                            onLazyRead: function(node){
                                node.appendAjax({
                                    url: "<?php echo $this->href_to('content', array('tree_ajax')); ?>",
                                    data: {
                                        id: node.data.key
                                    }
                                });
                            }

                        });
                    });

            </script>

        </td>
        <td class="main" valign="top">

            <?php $this->renderGrid($this->href_to('content', array('items_ajax', 1, 0)), $grid); ?>

        </td>
    </tr>
</table>

