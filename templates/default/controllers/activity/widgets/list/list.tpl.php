<?php if ($items){ ?>

    <?php 
        $last_date = ''; 
        $today_date = date('j F Y');
        $yesterday_date = date('j F Y', time()-3600*24);
    ?>

    <div class="widget_activity_list">
        <?php foreach($items as $item) { ?>

            <?php if ($show_date_groups) { ?>
                <?php $item_date = date('j F Y', strtotime($item['date_pub'])); ?>
                <?php if ($item_date != $last_date){ ?>

                    <?php
                        switch($item_date){
                            case $today_date: $date = LANG_TODAY; break;
                            case $yesterday_date: $date = LANG_YESTERDAY; break;
                            default: $date = lang_date($item_date);
                        }
                    ?>

                    <h4><?php echo $date; ?></h4>
                    <?php $last_date = $item_date; ?>

                <?php } ?>        
            <?php } ?>        
        
            <?php $url = href_to('users', $item['user']['id']); ?>

            <div class="item">
                <?php if ($show_avatars){ ?>
                <div class="image">
                    <a href="<?php echo $url; ?>"><?php echo html_avatar_image($item['user']['avatar'], 'micro'); ?></a>
                </div>
                <?php } ?>
                <div class="info">
                    <div class="title">
                        <a class="author" href="<?php echo $url; ?>"><?php html($item['user']['nickname']); ?></a>
                        <?php echo $item['description']; ?>
                        <?php if ($item['is_private']) { ?>
                            <span class="is_private" title="<?php html(LANG_PRIVACY_PRIVATE); ?>"></span>
                        <?php } ?>
                    </div>
                    <div class="details">
                        <span class="date"><?php echo $item['date_diff']; ?></span>
                        <?php if (!empty($item['reply_url']) && cmsUser::isLogged()) { ?>
                            <span class="reply">
                                <a href="<?php echo $item['reply_url']; ?>"><?php echo LANG_REPLY; ?></a>
                            </span>
                        <?php } ?>
                    </div>
                </div>
            </div>

        <?php } ?>
    </div>

<?php } ?>