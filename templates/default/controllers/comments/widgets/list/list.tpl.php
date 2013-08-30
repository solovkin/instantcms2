<?php if ($items){ ?>

    <div class="widget_comments_list">
        <?php foreach($items as $item) { ?>

            <?php $author_url = href_to('users', $item['user']['id']); ?>
            <?php $target_url = href_to($item['target_url']) . "#comment_{$item['id']}"; ?>

            <div class="item">
                <?php if ($show_avatars){ ?>
                <div class="image">
                    <a href="<?php echo $author_url; ?>"><?php echo html_avatar_image($item['user']['avatar'], 'micro'); ?></a>
                </div>
                <?php } ?>
                <div class="info">
                    <div class="title">
                        <a class="author" href="<?php echo $author_url; ?>"><?php html($item['user']['nickname']); ?></a>
                        &rarr;
                        <a class="subject" href="<?php echo $target_url; ?>"><?php html($item['target_title']); ?></a>
                        <span class="date">
                            <?php echo string_date_age_max($item['date_pub'], true); ?>
                        </span>
                        <?php if ($item['is_private']) { ?>
                            <span class="is_private" title="<?php html(LANG_PRIVACY_PRIVATE); ?>"></span>
                        <?php } ?>
                    </div>
                    <?php if ($show_text) { ?>
                        <div class="text">
                            <?php echo html_clean($item['content'], 50); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>

        <?php } ?>
    </div>

<?php } ?>