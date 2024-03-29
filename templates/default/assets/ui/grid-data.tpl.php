<form id="datagrid_filter">
    <?php if ($options['is_pagination']) { ?>
    <input type="hidden" name="page" value="1" />
    <input type="hidden" name="perpage" value="<?php echo admin::perpage; ?>" />
    <?php } ?>
    <input type="hidden" name="order_by" value="<?php echo $options['order_by']; ?>" />
    <input type="hidden" name="order_to" value="<?php echo $options['order_to']; ?>" />
    <?php foreach($columns as $name=>$column){ ?>
        <?php if (isset($column['filter'])){ ?>
            <?php echo html_input('hidden', $name); ?>
        <?php } ?>
    <?php } ?>
    <input type="hidden" id="advanced_filter" name="advanced_filter" value="" />
</form>

<form id="datagrid_form" action="" method="post">
</form>

<?php if ($options['is_toolbar']){ ?>
<div class="cp_toolbar">
    <?php $this->toolbar(); ?>
</div>
<?php } ?>

<div class="datagrid_wrapper">
    <table id="datagrid" class="datagrid <?php if ($options['is_selectable']) { ?>datagrid_selectable<?php } ?>" cellpadding="0" cellspacing="0" border="0">
        <thead>
            <tr>
                <?php foreach($columns as $name=>$column){ ?>
                    <?php if ($name=='id' && !$options['show_id']){ continue; } ?>
                    <th <?php if (isset($column['width'])){ ?>width="<?php echo $column['width']; ?>"<?php } ?> rel="<?php echo $name; ?>" <?php if($options['is_sortable']){ ?>class="sortable"<?php } ?>>
                        <?php echo $column['title']; ?>
                    </th>
                <?php } ?>
                <?php if ($actions) { ?>
                    <th width="<?php echo (sizeof($actions) * 30); ?>" class="center">
                        <?php echo LANG_CP_ACTIONS; ?>
                    </th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php if ($options['is_filter']){ ?>
            <tr class="filter">
                <?php foreach($columns as $name=>$column){ ?>
                    <td>
                        <?php if (isset($column['filter']) && $column['filter'] != 'none' && $column['filter'] != false){ ?>
                            <?php echo html_input('text', 'filter_'.$name, '', array('id'=>'filter_'.$name, 'rel'=>$name)); ?>
                        <?php } ?>
                    </td>
                <?php } ?>
                <?php if ($actions) { ?>
                    <td>
                        &nbsp;
                    </td>
                <?php } ?>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php if ($options['is_pagination']){ ?>
<div class="datagrid_navigation">
    <div class="datagrid_resize">
        <label>
            <?php echo LANG_PAGES_SHOW_PERPAGE; ?>
            <select>
                <?php for($p=5; $p<=50; $p+=5){ ?>
                    <option value="<?php echo $p; ?>" <?php if($p==admin::perpage){ ?>selected<?php } ?>><?php echo $p; ?></option>
                <?php } ?>
            </select>
        </label>
    </div>
    <div class="datagrid_pagination"></div>
</div>
<?php } ?>

<div class="datagrid_loading">
    <div class="indicator"><?php echo LANG_LOADING; ?></div>
</div>

<script type="text/javascript">

    <?php echo $this->getLangJS('LANG_LIST_EMPTY'); ?>
    <?php echo $this->getLangJS('LANG_LIST_NONE_SELECTED'); ?>

    icms.datagrid.setOptions({
        url: '<?php echo $source_url; ?>',
        pages_count: 0,
        perpage: <?php echo admin::perpage; ?>,
        show_id: <?php echo intval($options['show_id']); ?>,
        is_sortable: <?php echo intval($options['is_sortable']); ?>,
        is_filter: <?php echo intval($options['is_filter']); ?>,
        is_draggable: <?php echo intval($options['is_draggable']); ?>,
        is_actions: <?php echo intval($options['is_actions']); ?>,
        is_pagination: <?php echo intval($options['is_pagination']); ?>,
        is_selectable: <?php echo intval($options['is_selectable']); ?>,
        order_by: '<?php echo $options['order_by']; ?>',
        order_to: '<?php echo $options['order_to']; ?>'
    });

    <?php if ($options['is_auto_init']){ ?>
        icms.datagrid.init();
    <?php } ?>

</script>
