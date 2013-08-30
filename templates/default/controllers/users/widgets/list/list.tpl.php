<?php if ($profiles){ ?>

    <div class="widget_profiles_list">
        <?php foreach($profiles as $profile) { ?>

            <?php $url = href_to('users', $profile['id']); ?>

            <div class="item">
                <?php if ($is_avatars) { ?>
                    <div class="image">
                        <a href="<?php echo $url; ?>"><?php echo html_avatar_image($profile['avatar'], 'micro'); ?></a>
                    </div>
                <?php } ?>
                <div class="info">
                    <div class="name">
                        <a href="<?php echo $url; ?>"><?php html($profile['nickname']); ?></a>
                    </div>
                </div>
            </div>

        <?php } ?>
    </div>

<?php } ?>
