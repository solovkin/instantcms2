<?php // Шаблон списка комментариев и формы добавления // ?>

<?php $this->addJS('templates/default/js/jquery-scroll.js'); ?>
<?php $this->addJS('templates/default/js/comments.js'); ?>

<div id="comments_widget">

    <div class="title">
        <a name="comments"></a>
        <h2><?php echo $comments ? html_spellcount(sizeof($comments), LANG_COMMENT1, LANG_COMMENT2, LANG_COMMENT10) : LANG_COMMENTS; ?></h2>
        <?php if ($user->is_logged){ ?>
            <div class="track">
                <input type="checkbox" id="is_track" name="is_track" value="1" <?php if($is_tracking){ ?>checked="checked"<?php } ?> />
                <label for="is_track"><?php echo LANG_COMMENTS_TRACK; ?></label>
            </div>
        <?php } ?>
    </div>

    <?php if ($user->is_logged){ ?>
        <div id="comments_refresh_panel">
            <a href="#refresh" class="refresh_btn" onclick="icms.comments.refresh()" title="<?php echo LANG_COMMENTS_REFRESH; ?>"></a>
        </div>
    <?php } ?>

    <div id="comments_list">

        <?php if (!$comments){ ?>

            <div class="no_comments"><?php echo LANG_COMMENTS_NONE; ?></div>

        <?php } ?>

        <?php if ($comments){ ?>

            <?php echo $this->renderChild('comment', array('comments'=>$comments, 'user'=>$user, 'is_highlight_new'=>$is_highlight_new)); ?>

        <?php } ?>

    </div>

    <div id="comments_urls" style="display: none"
            data-get-url="<?php echo $this->href_to('get'); ?>"
            data-delete-url="<?php echo $this->href_to('delete'); ?>"
            data-refresh-url="<?php echo $this->href_to('refresh'); ?>"
            data-track-url="<?php echo $this->href_to('track'); ?>"
    ></div>

    <?php if (cmsUser::isAllowed('comments', 'add')){ ?>
    <div id="comments_add_link">
        <a href="#reply" class="ajaxlink" onclick="return icms.comments.add()"><?php echo LANG_COMMENT_ADD; ?></a>
    </div>

    <div id="comments_add_form">
        <div class="preview_box"></div>
        <form action="<?php echo $this->href_to('submit'); ?>" method="post">
            <?php echo html_csrf_token($csrf_token_seed); ?>
            <?php echo html_input('hidden', 'action', 'add'); ?>
            <?php echo html_input('hidden', 'id', 0); ?>
            <?php echo html_input('hidden', 'parent_id', 0); ?>
            <?php echo html_input('hidden', 'tc', $target_controller); ?>
            <?php echo html_input('hidden', 'ts', $target_subject); ?>
            <?php echo html_input('hidden', 'ti', $target_id); ?>
            <?php echo html_input('hidden', 'timestamp', time()); ?>
            <?php echo html_editor('content'); ?>
            <div class="buttons">
                <?php echo html_button(LANG_PREVIEW, 'preview', 'icms.comments.preview()'); ?>
                <?php echo html_button(LANG_SEND, 'submit', 'icms.comments.submit()'); ?>
            </div>
            <div class="loading">
                <?php echo LANG_LOADING; ?>
            </div>
        </form>
    </div>

    <script>
        <?php echo $this->getLangJS('LANG_SEND'); ?>
        <?php echo $this->getLangJS('LANG_SAVE'); ?>
        <?php echo $this->getLangJS('LANG_COMMENT_DELETED'); ?>
        <?php echo $this->getLangJS('LANG_COMMENT_DELETE_CONFIRM'); ?>
        <?php if ($is_highlight_new){ ?>icms.commments.showFirstSelected();<?php } ?>
    </script>
    <?php } ?>

</div>
