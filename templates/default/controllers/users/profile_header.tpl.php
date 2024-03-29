<?php $this->addJS('templates/default/js/users.js'); ?>
<?php $user = cmsUser::getInstance(); ?>
<h1 id="user_profile_title">

    <div class="avatar"><?php echo html_avatar_image($profile['avatar'], 'micro'); ?></div>

    <div id="user_profile_rates" class="rates"
         data-url="<?php echo $this->href_to('karma_vote', $profile['id']); ?>"
         data-log-url="<?php echo $this->href_to('karma_log', $profile['id']); ?>"
         data-is-comment="<?php echo $this->controller->options['is_karma_comments']; ?>">
        <div class="karma block">
            <?php if ($profile['is_can_vote_karma']){ ?>
                <a href="#vote-up" onclick="return icms.users.karmaUp()" class="thumb thumb_up" title="<?php echo LANG_KARMA_UP; ?>"></a>
            <?php } ?>
            <span class="value <?php echo html_signed_class($profile['karma']); ?>" title="<?php echo LANG_KARMA; ?>">
                <?php echo html_signed_num($profile['karma']); ?>
            </span>
            <?php if ($profile['is_can_vote_karma']){ ?>
                <a href="#vote-down" onclick="return icms.users.karmaDown()" class="thumb thumb_down" title="<?php echo LANG_KARMA_DOWN; ?>"></a>
            <?php } ?>
        </div>
        <?php if ($this->controller->options['is_karma_comments']) { ?>
            <script><?php echo $this->getLangJS('LANG_USERS_KARMA_COMMENT'); ?></script>
        <?php } ?>
    </div>

    <div class="name<?php if ($profile['status']){ ?> name_with_status<?php } ?>">
        
        <?php html($profile['nickname']); ?>
        <?php if ($profile['is_locked']){ ?>
            <span class="is_locked"><?php echo LANG_USERS_LOCKED_NOTICE_PUBLIC; ?></span>
        <?php } ?>
            
        <?php if ($this->controller->options['is_status']) { ?>
            <div class="status" <?php if (!$profile['status']){ ?>style="display:none"<?php } ?>>
                <span class="text">
                    <?php if ($profile['status']) { ?>
                        <?php html($profile['status']['content']); ?>
                    <?php } ?>
                </span>
                <?php if ($user->is_logged){ ?>
                    <span class="reply">
                        <?php if (empty($profile['status']['replies_count'])) { ?>
                            <a href="<?php echo $this->href_to($profile['id']) . "?wid={$profile['status']['wall_entry_id']}&reply=1"; ?>"><?php echo LANG_REPLY; ?></a>
                        <?php } else { ?>
                            <a href="<?php echo $this->href_to($profile['id']) . "?wid={$profile['status']['wall_entry_id']}"; ?>"><?php echo html_spellcount($profile['status']['replies_count'], LANG_REPLY_SPELLCOUNT); ?></a>
                        <?php } ?>
                    </span>
                    <?php if ($profile['id'] == $user->id) { ?>
                        <span class="delete">
                            <a href="#delete-status" onclick="return icms.users.deleteStatus(this)" data-url="<?php echo $this->href_to('status_delete', $profile['id']); ?>"><?php echo LANG_DELETE; ?></a>
                        </span>
                    <?php } ?>
                <?php } ?>
            </div>
        <?php } ?>
            
    </div>

</h1>

<?php if ($this->controller->options['is_status'] && $profile['id'] == $user->id) { ?>
    <script><?php
        echo $this->getLangJS('LANG_REPLY');
        echo $this->getLangJS('LANG_USERS_DELETE_STATUS_CONFIRM');
    ?></script>
    <div id="user_status_widget">
        <?php
            echo html_input('text', 'status', '', array(
                'maxlength' => 140,
                'placeholder' => sprintf(LANG_USERS_WHAT_HAPPENED, $profile['nickname']),
                'data-url' => $this->href_to('status'),
                'data-user-id' => $profile['id']
            ));
        ?>
    </div>
<?php } ?>

<?php if (!isset($is_can_view) || $is_can_view){ ?>

    <?php if (empty($tabs)){ $tabs = $this->controller->getProfileMenu($profile); } ?>

    <?php $this->addMenuItems('profile_tabs', $tabs); ?>

    <div id="user_profile_tabs">
        <div class="tabs-menu">
            <?php $this->menu('profile_tabs', true, '', $this->controller->options['max_tabs']); ?>
        </div>
    </div>

<?php } ?>