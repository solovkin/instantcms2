<?php if ($do=='add') { ?><h1><?php echo LANG_CP_DATASET_ADD; ?></h1><?php } ?>
<?php if ($do=='edit') { ?><h1><?php echo LANG_CP_DATASET; ?>: <span><?php echo $dataset['title']; ?></span></h1><?php } ?>

<?php

    $this->setPageTitle(LANG_CP_CTYPE_DATASETS);

    $this->addBreadcrumb(LANG_CP_SECTION_CTYPES, $this->href_to('ctypes'));

    if ($do=='add'){
        $this->addBreadcrumb($ctype['title'], $this->href_to('ctypes', array('edit', $ctype['id'])));
        $this->addBreadcrumb(LANG_CP_CTYPE_DATASETS, $this->href_to('ctypes', array('datasets', $ctype['id'])));
        $this->addBreadcrumb(LANG_CP_DATASET_ADD);
    }

    if ($do=='edit'){
        $this->addBreadcrumb($ctype['title'], $this->href_to('ctypes', array('edit', $ctype['id'])));
        $this->addBreadcrumb(LANG_CP_CTYPE_DATASETS, $this->href_to('ctypes', array('datasets', $ctype['id'])));
        $this->addBreadcrumb($dataset['title']);
    }

    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));
    $this->addToolButton(array(
        'class' => 'cancel',
        'title' => LANG_CANCEL,
        'href'  => $this->href_to('ctypes', array('datasets', $ctype['id']))
    ));
    $this->addToolButton(array(
		'class' => 'help',
		'title' => LANG_HELP,
		'target' => '_blank',
		'href'  => LANG_HELP_URL_CTYPES_DATASET
	));

    unset($fields['user']);

?>

<div class="cp_toolbar"><?php echo $this->menu('toolbar'); ?></div>

<form id="dataset_form" action="" method="post" enctype="multipart/form-data">

    <?php echo html_csrf_token(); ?>

    <fieldset>

        <?php
            $name = 'name';
            if (is_array($errors) && isset($errors[$name])){ $error = $errors[$name]; } else { $error = false; }
            if (array_key_exists($name, $dataset)){ $value = $dataset[$name]; } else { $value = null; }
        ?>
        <div class="field <?php if ($error){ ?>field_error<?php } ?>" id="f_name">
            <?php if ($error){ ?><div class="error_text"><?php echo $error; ?></div><?php } ?>
            <label for="<?php echo $name; ?>"><?php echo LANG_SYSTEM_NAME; ?></label>
            <?php echo html_input('text', $name, $value, array('id'=>$name, 'class'=>$error?'error':'')); ?>
        </div>

        <?php
            $name = 'title';
            if (is_array($errors) && isset($errors[$name])){ $error = $errors[$name]; } else { $error = false; }
            if (array_key_exists($name, $dataset)){ $value = $dataset[$name]; } else { $value = null; }
        ?>
        <div class="field <?php if ($error){ ?>field_error<?php } ?>" id="f_title">
            <?php if ($error){ ?><div class="error_text"><?php echo $error; ?></div><?php } ?>
            <label for="<?php echo $name; ?>"><?php echo LANG_CP_DATASET_TITLE; ?></label>
            <?php echo html_input('text', $name, $value, array('id'=>$name, 'class'=>$error?'error':'')); ?>
        </div>

        <?php
            $name = 'is_visible';
            if (is_array($errors) && isset($errors[$name])){ $error = $errors[$name]; } else { $error = false; }
            if (array_key_exists($name, $dataset)){ $value = $dataset[$name]; } else { $value = false; }
        ?>
        <div class="field <?php if ($error){ ?>field_error<?php } ?>" id="f_is_visible">
            <label for="<?php echo $name; ?>">
                <?php echo html_checkbox($name, $value, 1, array('id'=>$name)); ?>
                <?php echo LANG_CP_DATASET_IS_VISIBLE; ?>
            </label>
        </div>

    </fieldset>

    <fieldset>

        <legend><?php echo LANG_SHOW_TO_GROUPS; ?></legend>

        <?php
            $name = 'groups_view';
            if (is_array($errors) && isset($errors[$name])){ $error = $errors[$name]; } else { $error = false; }
            if (array_key_exists($name, $dataset)){ $value = $dataset[$name]; } else { $value = false; }
        ?>
        <div class="field <?php if ($error){ ?>field_error<?php } ?>" id="f_groups_view">
            <?php if ($error){ ?><div class="error_text"><?php echo $error; ?></div><?php } ?>
            <?php $widget = new fieldListGroups($name, array('show_all'=>true, 'show_guests'=>true)); ?>
            <?php echo $widget->getInput($value); ?>
        </div>

    </fieldset>

    <fieldset>

        <legend><?php echo LANG_HIDE_FOR_GROUPS; ?></legend>

        <?php
            $name = 'groups_hide';
            if (is_array($errors) && isset($errors[$name])){ $error = $errors[$name]; } else { $error = false; }
            if (array_key_exists($name, $dataset)){ $value = $dataset[$name]; } else { $value = false; }
        ?>
        <div class="field <?php if ($error){ ?>field_error<?php } ?>" id="f_groups_view">
            <?php if ($error){ ?><div class="error_text"><?php echo $error; ?></div><?php } ?>
            <?php $widget = new fieldListGroups($name, array('show_all'=>false, 'show_guests'=>true)); ?>
            <?php echo $widget->getInput($value); ?>
        </div>

    </fieldset>

    <fieldset>

        <legend><?php echo LANG_SORTING; ?></legend>

        <?php $sort_by = isset($dataset['sorting']['by']) ? $dataset['sorting']['by'] : 'date_pub'; ?>
        <?php $sort_to = isset($dataset['sorting']['to']) ? $dataset['sorting']['to'] : 'desc'; ?>

        <select name="sorting[by]">
            <?php foreach($fields as $field){ ?>
                <option value="<?php echo $field['name']; ?>" <?php if ($field['name']==$sort_by){ ?>selected="selected"<?php } ?>><?php echo htmlspecialchars($field['title']); ?></option>
            <?php } ?>
            <option value="rating" <?php if ('rating'==$sort_by){ ?>selected="selected"<?php } ?>><?php echo LANG_RATING; ?></option>
        </select>
        <select name="sorting[to]">
            <option value="asc" <?php if ('asc'==$sort_to){ ?>selected="selected"<?php } ?>><?php echo LANG_SORTING_ASC; ?></option>
            <option value="desc" <?php if ('desc'==$sort_to){ ?>selected="selected"<?php } ?>><?php echo LANG_SORTING_DESC; ?></option>
        </select>

    </fieldset>

    <fieldset>

        <legend><?php echo LANG_FILTERS; ?></legend>

        <div id="filters"></div>

        <div id="add_filter" style="display:none">
            <?php echo LANG_FILTER_FIELD; ?>:
            <select></select>
            <a class="ajaxlink" href="javascript:submitFilter()"><?php echo LANG_ADD; ?></a> |
            <a class="ajaxlink" href="javascript:cancelFilter()"><?php echo LANG_CANCEL; ?></a>
        </div>

        <a id="add_filter_link" class="ajaxlink" href="javascript:addFilter()"><?php echo LANG_FILTER_ADD; ?></a>

    </fieldset>

    <div class="buttons">
        <?php echo html_submit(LANG_SAVE); ?>
    </div>

</form>

<div id="filter_template" class="filter" style="display:none">
    <span class="title"><input type="hidden" name="" value="" /></span>
    <span class="condition"><select name=""></select></span>
    <span class="value"><input class="input" type="text" name="" /></span>
    <span class="delete"><a class="ajaxlink" href="javascript:" onclick="deleteFilter(this)"><?php echo LANG_DELETE; ?></a></span>
</div>

<select id="fields_list" style="display:none">
    <?php foreach($fields as $field){ ?>
        <option value="<?php echo $field['name']; ?>" data-type="<?php echo $field['handler']->filter_type; ?>"><?php echo htmlspecialchars($field['title']); ?></option>
    <?php } ?>
    <option value="rating" data-type="int"><?php echo LANG_RATING; ?></option>
    <option value="user_id" data-type="int"><?php echo LANG_AUTHOR; ?></option>
</select>

<select id="conditions_int" style="display:none">
    <option value="eq">=</option>
    <option value="gt">&gt;</option>
    <option value="lt">&lt;</option>
    <option value="ge">&ge;</option>
    <option value="le">&le;</option>
    <option value="nn"><?php echo LANG_FILTER_NOT_NULL; ?></option>
    <option value="ni"><?php echo LANG_FILTER_IS_NULL; ?></option>
</select>

<select id="conditions_str" style="display:none">
    <option value="eq">=</option>
    <option value="lk"><?php echo LANG_FILTER_LIKE; ?></option>
    <option value="lb"><?php echo LANG_FILTER_LIKE_BEGIN; ?></option>
    <option value="lf"><?php echo LANG_FILTER_LIKE_END; ?></option>
    <option value="nn"><?php echo LANG_FILTER_NOT_NULL; ?></option>
    <option value="ni"><?php echo LANG_FILTER_IS_NULL; ?></option>
</select>

<select id="conditions_date" style="display:none">
    <option value="eq">=</option>
    <option value="gt">&gt;</option>
    <option value="lt">&lt;</option>
    <option value="ge">&ge;</option>
    <option value="le">&le;</option>
    <option value="dy"><?php echo LANG_FILTER_DATE_YOUNGER; ?></option>
    <option value="do"><?php echo LANG_FILTER_DATE_OLDER; ?></option>
    <option value="nn"><?php echo LANG_FILTER_NOT_NULL; ?></option>
    <option value="ni"><?php echo LANG_FILTER_IS_NULL; ?></option>
</select>

<script type="text/javascript">

    function addFilter(){
        $('#add_filter select').html($('#fields_list').html()).show();
        $('#add_filter').show();
        $('#add_filter_link').hide();
    }

    function submitFilter(data){

        if (typeof(data) == 'undefined') {
            data = {field: false, condition: false, value: false}
        }

        if (data.field){
            var field = data.field;
        } else {
            var field = $('#add_filter select').val();
        }

        var filter_id = $('#filters .filter').length;
        var filter = $('#filter_template').clone();

        var field_title = $('#fields_list option[value='+field+']').html();
        var field_type = $('#fields_list option[value='+field+']').data('type');

        $(filter).attr('id', 'filter'+filter_id);

        $('.title', filter).append(field_title);
        $('.condition select', filter).html( $('#conditions_' + field_type).html() );

        $('.title input', filter).attr('name', 'filters['+filter_id+'][field]').val(field);
        $('.condition select', filter).attr('name', 'filters['+filter_id+'][condition]');
        $('.value input', filter).attr('name', 'filters['+filter_id+'][value]');

        if (data.condition) {
            $('.condition select', filter).val(data.condition);
        }

        if (data.value) {
            $('.value input', filter).val(data.value);
        }

        $('#filters').append(filter);

        $('#filters #filter'+filter_id).slideToggle(300);

        cancelFilter();

    }

    function cancelFilter(){
        $('#add_filter').hide();
        $('#add_filter_link').show();
    }

    function deleteFilter(link_instance){
        $(link_instance).parent('span').parent('div').slideToggle(300, function(){ $(this).remove() });
    }

    <?php if (!empty($dataset['filters'])){ ?>
        <?php foreach($dataset['filters'] as $filter) { ?>
            <?php if (!empty($filter['condition'])){ ?>
                submitFilter({
                    field: '<?php echo $filter['field']; ?>',
                    condition: '<?php echo !empty($filter['condition']) ? $filter['condition'] : ''; ?>',
                    value: '<?php echo $filter['value']; ?>'
                });
            <?php } ?>
        <?php } ?>
    <?php } ?>

</script>