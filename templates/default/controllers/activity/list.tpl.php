<?php if ($items){ ?>

    <?php 
        $last_date = ''; 
        $today_date = date('j F Y');
        $yesterday_date = date('j F Y', time()-3600*24);
    ?>

    <div class="activity-list striped-list list-32">
        <?php foreach($items as $item) { ?>

            <?php $item_date = date('j F Y', strtotime($item['date_pub'])); ?>
            <?php if ($item_date != $last_date){ ?>
        
                <?php
                    switch($item_date){
                        case $today_date: $date = LANG_TODAY; break;
                        case $yesterday_date: $date = LANG_YESTERDAY; break;
                        default: $date = lang_date($item_date);
                    }
                ?>
        
                <h3><?php echo $date; ?></h3>
                <?php $last_date = $item_date; ?>
        
            <?php } ?>
        
            <?php $url = href_to('users', $item['user']['id']); ?>

            <div class="item">
                <div class="icon">
                    <a href="<?php echo $url; ?>"><?php echo html_avatar_image($item['user']['avatar'], 'micro'); ?></a>
                </div>
                <div class="title-multiline">
                    <a class="author" href="<?php echo $url; ?>"><?php html($item['user']['nickname']); ?></a>
                    <?php echo $item['description']; ?>
                    <?php if ($item['is_private']) { ?>
                        <span class="is_private" title="<?php html(LANG_PRIVACY_PRIVATE); ?>"></span>
                    <?php } ?>
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

    <?php if ($perpage < $total) { ?>
        <?php echo html_pagebar($page, $perpage, $total, $page_url, $filters); ?>
    <?php } ?>

<?php } else { echo LANG_LIST_EMPTY; } ?>
