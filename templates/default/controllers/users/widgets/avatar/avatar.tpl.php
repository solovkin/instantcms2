<div class="widget_user_avatar">

    <div class="avatar">
        <a href="<?php echo href_to('users', $user->id); ?>">
            <?php echo html_avatar_image($user->avatar, 'micro'); ?>
        </a>
    </div>

    <div class="info">
        <div class="name">
            <a href="<?php echo href_to('users', $user->id); ?>">
                <?php html($user->nickname); ?>
            </a>
        </div>
        <div class="stats">
            <div class="karma">
                <?php echo LANG_KARMA; ?>:
                <span class="<?php echo html_signed_class($user->karma); ?>"><?php echo html_signed_num($user->karma); ?></span>
            </div>
            <div class="rating">
                <?php echo LANG_RATING; ?>:
                <span><?php echo $user->rating; ?></span>
            </div>
        </div>
    </div>

</div>
