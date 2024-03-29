<?php

    $this->addJS('templates/default/js/admin-moderators.js');
    $this->addJS('templates/default/js/jquery-ui.js');
    $this->addCSS('templates/default/css/jquery-ui.css');

    $this->setPageTitle(LANG_MODERATORS);

    $this->addBreadcrumb(LANG_CP_SECTION_CTYPES, $this->href_to('ctypes'));
    $this->addBreadcrumb($ctype['title'], $this->href_to('ctypes', array('edit', $ctype['id'])));
    $this->addBreadcrumb(LANG_MODERATORS);

    $this->addMenuItems('ctype', $this->controller->getCtypeMenu('moderators', $ctype['id']));

	$this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_CTYPES_MODERATORS
	));

?>


<h1><?php echo LANG_CONTENT_TYPE; ?>: <span><?php echo $ctype['title']; ?></span></h1>

<div class="pills-menu">
    <?php $this->menu('ctype'); ?>
</div>

<div class="cp_toolbar">
    <?php $this->menu('toolbar'); ?>
</div>

<div id="ctype_moderators_list" class="striped-list list-32" <?php if (!$moderators){ ?>style="display:none"<?php } ?>>

    <div class="datagrid_wrapper">
        <table id="datagrid" class="datagrid <?php if ($options['is_selectable']) { ?>datagrid_selectable<?php } ?>" cellpadding="0" cellspacing="0" border="0">
            <thead>
                <tr>
                    <th colspan="2"><?php echo LANG_MODERATOR; ?></th>
                    <th class="center"><?php echo LANG_MODERATOR_ASSIGNED_DATE; ?></th>
                    <th class="center"><?php echo LANG_MODERATOR_APPROVED_COUNT; ?></th>
                    <th class="center"><?php echo LANG_MODERATOR_DELETED_COUNT; ?></th>
                    <th class="center"><?php echo LANG_MODERATOR_IDLE_COUNT; ?></th>
                    <th class="center" width="32"><?php echo LANG_CP_ACTIONS; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($moderators){ ?>
                    <?php foreach($moderators as $moderator) { ?>
                        <?php echo $this->renderChild('ctypes_moderator', array('moderator'=>$moderator, 'ctype'=>$ctype)); ?>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

<div id="ctype_moderators_add" class="gui-panel">

    <h3><?php echo LANG_MODERATOR_ADD; ?></h3>
    <div class="hint"><?php echo LANG_MODERATOR_ADD_HINT; ?></div>

    <div class="field">
        <?php echo html_input('text', 'username', '', array('id'=>'username', 'autocomplete'=>'off')); ?>
        <?php echo html_button(LANG_ADD, 'add', 'return icms.adminModerators.add()', array('id'=>'submit', 'disabled'=>'disabled')); ?>
    </div>
    <div class="loading-icon" style="display:none"></div>

</div>

<script>

    $(document).ready(function(){

        icms.adminModerators.url_submit = '<?php echo $this->href_to('ctypes', array('moderators', $ctype['id'],  'add')); ?>';
        icms.adminModerators.url_delete = '<?php echo $this->href_to('ctypes', array('moderators', $ctype['id'],  'delete')); ?>';

        var cache = {};

        $( "#username" ).autocomplete({
            minLength: 2,
            delay: 500,
            source: function( request, response ) {

                var term = request.term;

                if ( term in cache ) {
                    response( cache[ term ] );
                    return;
                }

                $.getJSON('<?php echo $this->href_to('users', 'autocomplete'); ?>', request, function( data, status, xhr ) {
                    cache[ term ] = data;
                    response( data );
                });

            }
        });

        $( "#submit" ).removeAttr('disabled');
        $('#ctype_moderators_list #datagrid tr:odd').addClass('odd');

    });

</script>
