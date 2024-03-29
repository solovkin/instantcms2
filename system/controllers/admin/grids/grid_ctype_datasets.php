<?php

function grid_ctype_datasets($controller){

    $options = array(
        'is_sortable' => false,
        'is_filter' => false,
        'is_pagination' => false,
        'is_draggable' => true,
        'order_by' => 'ordering',
        'order_to' => 'asc',
        'show_id' => false
    );

    $columns = array(
        'id' => array(
            'title' => 'id',
            'width' => 30,
        ),
        'title' => array(
            'title' => LANG_CP_DATASET_TITLE,
            'href' => href_to($controller->name, 'ctypes', array('datasets_edit', '{ctype_id}', '{id}')),
        ),
        'name' => array(
            'title' => LANG_SYSTEM_NAME,
            'width' => 150,
        ),        
        'is_visible' => array(
            'title' => LANG_PUBLICATION,
            'flag'  => true,
            'width' => 90,
        )
    );

    $actions = array(
        array(
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href' => href_to($controller->name, 'ctypes', array('datasets_edit', '{ctype_id}', '{id}'))
        ),
        array(
            'title' => LANG_DELETE,
            'class' => 'delete',
            'href' => href_to($controller->name, 'ctypes', array('datasets_delete', '{id}')),
            'confirm' => LANG_CP_DATASET_DELETE_CONFIRM
        )
    );

    return array(
        'options' => $options, 
        'columns' => $columns,
        'actions' => $actions
    );
    
}

