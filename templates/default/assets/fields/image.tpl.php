<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php

    if ($value){
        $paths = is_array($value) ? $value : cmsModel::yamlToArray($value);
    } else {
        $paths = false;
    }

    $images_controller = cmsCore::getController('images', new cmsRequest(array(
        'name' => $field->element_name,
        'paths' => $paths
    ), cmsRequest::CTX_INTERNAL));

    echo $images_controller->runAction('get_single_upload_widget');

?>
