<?php

    $this->addJS('templates/default/js/jquery-ui.js');
    $this->addCSS('templates/default/css/jquery-ui.css');

    $this->setPageTitle($profile['nickname']);

    $this->addBreadcrumb(LANG_USERS, href_to('users'));
    $this->addBreadcrumb($profile['nickname']);

    $tool_buttons = array();

    if ($user->is_logged) {

        if ($is_friends_on && !$is_own_profile){
            if ($is_friend_profile){
                $tool_buttons['friend_delete'] = array(
                    'title' => LANG_USERS_FRIENDS_DELETE,
                    'class' => 'user_delete',
                    'href' => $this->href_to('friend_delete', $profile['id'])
                );
            } else if(!$is_friend_req) {
                $tool_buttons['friend_add'] = array(
                    'title' => LANG_USERS_FRIENDS_ADD,
                    'class' => 'user_add',
                    'href' => $this->href_to('friend_add', $profile['id'])
                );
            }
        }

        if ($is_own_profile || $user->is_admin){
            $tool_buttons['settings'] = array(
                'title' => LANG_USERS_EDIT_PROFILE,
                'class' => 'settings',
                'href' => $this->href_to($profile['id'], 'edit')
            );
        }

        if ($user->is_admin){
            $tool_buttons['edit'] = array(
                'title' => LANG_USERS_EDIT_USER,
                'class' => 'edit',
                'href' => href_to('admin', 'users', array('edit', $profile['id'])) . "?back=" . $this->href_to($profile['id'])
            );
        }

    }

    $buttons_hook = cmsEventsManager::hook('user_profile_buttons', array(
        'profile' => $profile,
        'buttons' => $tool_buttons
    ));

    $tool_buttons = $buttons_hook['buttons'];

    if (is_array($tool_buttons)){
        foreach($tool_buttons as $button){
            $this->addToolButton($button);
        }
    }

?>

<div id="user_profile_header">
    <?php $this->renderChild('profile_header', array('profile'=>$profile, 'tabs'=>$tabs)); ?>
</div>

<div id="user_profile">

    <div id="left_column" class="column">

        <div id="avatar" class="block">
            <?php echo html_avatar_image($profile['avatar'], 'normal'); ?>
        </div>

        <?php if ($is_friends_on && $friends) { ?>
            <div class="block">
                <div class="block-title">
                    <a href="<?php echo $this->href_to($profile['id'], 'friends'); ?>"><?php echo LANG_USERS_FRIENDS; ?></a>
                    (<?php echo $profile['friends_count']; ?>)
                </div>
                <div class="friends-list">
                    <?php foreach($friends as $friend){ ?>
                        <a href="<?php echo $this->href_to($friend['id']); ?>" title="<?php html($friend['nickname']); ?>">
                            <span><?php echo html_avatar_image($friend['avatar'], 'micro'); ?></span>
                        </a>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <div class="block">

            <ul class="details">

                <li>
                    <strong><?php echo LANG_RATING; ?>:</strong>
                    <span class="<?php echo html_signed_class($profile['rating']); ?>"><?php echo $profile['rating']; ?></span>
                </li>

                <li>
                    <strong><?php echo LANG_USERS_PROFILE_REGDATE; ?>:</strong>
                    <?php echo string_date_age_max($profile['date_reg'], true); ?>
                </li>

                <li>
                    <strong><?php echo LANG_USERS_PROFILE_LOGDATE; ?>:</strong>
                    <?php echo $profile['is_online'] ? '<span class="online">'.LANG_ONLINE.'</span>' : string_date_age_max($profile['date_log'], true); ?>
                </li>

            </ul>

        </div>

    </div>

    <div id="right_column" class="column">

            <div id="information" class="content_item block">

                <?php
                    $fieldsets = cmsForm::mapFieldsToFieldsets($fields, function($field, $user){
                        if (in_array($field['name'], array('nickname', 'avatar'))){ return false; }
                        return true;
                    }, $profile);
                ?>

                <?php foreach($fieldsets as $fieldset){ ?>

                    <?php if (!$fieldset['fields']) { continue; } ?>

                    <div class="fieldset">

                    <?php if ($fieldset['title']){ ?>
                        <div class="fieldset_title">
                            <h3><?php echo $fieldset['title']; ?></h3>
                        </div>
                    <?php } ?>

                    <?php foreach($fieldset['fields'] as $field){ ?>

                        <?php if (empty($profile[$field['name']])) { continue; } ?>

                        <?php
                            if (!isset($field['options']['label_in_item'])) {
                                $label_pos = 'none';
                            } else {
                                $label_pos = $field['options']['label_in_item'];
                            }
                        ?>

                        <div class="field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?>">

                            <?php if ($label_pos != 'none'){ ?>
                                <div class="title title_<?php echo $label_pos; ?>"><?php echo $field['title']; ?>: </div>
                            <?php } ?>

                            <div class="value">

                                <?php

                                    echo $field['handler']->parse( $profile[$field['name']] );

                                ?>

                            </div>

                        </div>

                    <?php } ?>

                    </div>

                <?php } ?>

            </div>

    </div>

</div>

<?php if ($wall_html){ ?>
    <div id="user_profile_wall">
        <?php echo $wall_html; ?>
    </div>
<?php } ?>

<script>
    $(function() {
        $('.friends-list a').tooltip({
            show: { duration: 0 },
            hide: { duration: 0 },
            position: {
                my: "center+5 top+2",
                at: "center bottom"
            }
        });
    });
</script>
