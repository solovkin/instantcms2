<?php

    $this->setPageTitle(LANG_USERS_KARMA_LOG, $profile['nickname']);

    $this->addBreadcrumb(LANG_USERS, $this->href_to(''));
    $this->addBreadcrumb($profile['nickname'], $this->href_to($profile['id']));
    $this->addBreadcrumb(LANG_USERS_KARMA_LOG);

?>

<div id="user_profile_header">
    <?php $this->renderChild('profile_header', array('profile'=>$profile)); ?>
</div>

<div id="users_karma_log_window">

    <?php if ($log){ ?>

        <div id="users_karma_log_list" class="striped-list list-32">

            <?php foreach($log as $entry){ ?>

                <div class="item">

                    <div class="icon">
                        <?php echo html_avatar_image($entry['user']['avatar'], 'micro'); ?>
                    </div>

                    <div class="value <?php echo html_signed_class($entry['points']); ?>">
                        <span>
                            <?php echo html_signed_num($entry['points']); ?>
                        </span>
                    </div>

                    <div class="title<?php if ($entry['comment']){ ?>-multiline<?php } ?>">

                        <a href="<?php echo $this->href_to($entry['user']['id']); ?>"><?php html($entry['user']['nickname']); ?></a>
                        <span class="date"><?php echo string_date_age_max($entry['date_pub'], true); ?></span>

                        <?php if ($entry['comment']){ ?>
                            <div class="comment">
                                <?php html($entry['comment']); ?>
                            </div>
                        <?php } ?>
                    </div>

                </div>

            <?php } ?>

        </div>

    <?php } ?>

    <?php if (!$log){ ?>
        <p><?php echo LANG_LIST_EMPTY; ?></p>
    <?php } ?>

</div>

<?php if ($perpage < $total) { ?>
    <?php echo html_pagebar($page, $perpage, $total); ?>
<?php } ?>
