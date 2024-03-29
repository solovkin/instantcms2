<?php $this->setPageTitle(LANG_REGISTRATION); ?>

<?php $this->addBreadcrumb(LANG_REGISTRATION); ?>

<h1><?php echo LANG_REGISTRATION; ?></h1>

<?php
    $this->renderForm($form, $user, array(
        'action' => '',
        'method' => 'post',
        'append_html' => $captcha_html,
        'submit' => array(
            'title' => LANG_CONTINUE
        )
    ), $errors);
?>

<script>

    function toggleGroups(){

        if ($('select#group_id').length == 0){ return false; }

        var group_id = $('select#group_id').val();

        $('.groups-limit').hide();

        $('.group-' + group_id).show();

        $('fieldset').each(function(){

           if ($('.field:visible', $(this)).length==0) {
               $(this).hide();
           }

           if ($('.group-' + group_id, $(this)).length>0) {
               $(this).show();
           }

        });

    }

    $(document).ready(function(){

        if ($('select#group_id').length == 0){ return false; }

        $('select#group_id').change(function(){ toggleGroups(); });

    });

    toggleGroups();

</script>