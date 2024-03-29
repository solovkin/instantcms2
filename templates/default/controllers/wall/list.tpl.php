<?php $this->addJS('templates/default/js/jquery-scroll.js'); ?>
<?php $this->addJS('templates/default/js/wall.js'); ?>
<a name="wall"></a>
<div id="wall_widget">

    <div class="title_bar">
        <h2 class="title"><?php echo $title; ?></h2>
        <?php if ($permissions['add']){ ?>
            <a href="#wall-write" id="wall_add_link" onclick="return icms.wall.add()">
                <?php echo LANG_WALL_ENTRY_ADD; ?>
            </a>
        <?php } ?>
    </div>

    <div id="wall_urls" style="display: none"
            data-get-url="<?php echo $this->href_to('get'); ?>"
            data-replies-url="<?php echo $this->href_to('get_replies'); ?>"
            data-delete-url="<?php echo $this->href_to('delete'); ?>"
    ></div>

    <div id="wall_add_form">
        <div class="preview_box"></div>
        <form action="<?php echo $this->href_to('submit'); ?>" method="post">
            <?php echo html_csrf_token($csrf_token_seed); ?>
            <?php echo html_input('hidden', 'action', 'add'); ?>
            <?php echo html_input('hidden', 'id', 0); ?>
            <?php echo html_input('hidden', 'parent_id', 0); ?>
            <?php echo html_input('hidden', 'pc', $controller); ?>
            <?php echo html_input('hidden', 'pt', $profile_type); ?>
            <?php echo html_input('hidden', 'pi', $profile_id); ?>
            <?php echo html_editor('content'); ?>
            <div class="buttons">
                <?php echo html_button(LANG_PREVIEW, 'preview', 'icms.wall.preview()'); ?>
                <?php echo html_button(LANG_SEND, 'submit', 'icms.wall.submit()'); ?>
            </div>
            <div class="loading">
                <?php echo LANG_LOADING; ?>
            </div>
        </form>
    </div>

    <div id="entries_list">

        <?php if (!$entries) { ?>
            <p class="no_entries"><?php echo LANG_WALL_EMPTY; ?></p>
        <?php } ?>

        <?php if ($entries){ ?>
            <?php

                echo $this->renderChild('entry', array(
                    'entries'=>$entries,
                    'max_entries'=>$max_entries,
                    'page'=>$page,
                    'user'=>$user,
                    'permissions'=>$permissions
                ));

            ?>
        <?php } ?>

    </div>

    <?php if ($perpage < $total) { ?>
        <div class="wall_pages" <?php if($max_entries && (sizeof($entries) > $max_entries) && $page==1) {?>style="display:none"<?php } ?>>
            <?php echo html_pagebar($page, $perpage, $total, '#wall'); ?>
        </div>
    <?php } ?>

    <script>
        <?php echo $this->getLangJS('LANG_SEND'); ?>
        <?php echo $this->getLangJS('LANG_SAVE'); ?>
        <?php echo $this->getLangJS('LANG_WALL_ENTRY_DELETED'); ?>
        <?php echo $this->getLangJS('LANG_WALL_ENTRY_DELETE_CONFIRM'); ?>

        <?php if ($show_id) { ?>
            <?php if ($go_reply && !$user->is_logged) { $go_reply = false; } ?>
            icms.wall.show(<?php echo $show_id; ?>, <?php echo $show_reply_id; ?>, <?php echo $go_reply; ?>);
        <?php } ?>
    </script>

</div>
