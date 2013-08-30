<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php

    $items = $field->getListItems();
    
    $is_multiple = $field->getProperty('is_multiple');

    if (!$is_multiple){
    
        echo html_select($field->element_name, $items, $value, array('id'=>$field->id));
        
    } else {
        
        echo html_select_multiple($field->element_name, $items, $value, array('id'=>$field->id));
        
    }
