<?php

    $this->addJS( $this->getJavascriptFileName('photos') );

    $this->setPageTitle($photo['title']);

    $user = cmsUser::getInstance();

    if ($ctype['options']['list_on']){
        $this->addBreadcrumb($ctype['title'], href_to($ctype['name']));
    }

    if (isset($album['category'])){
        foreach($album['category']['path'] as $c){
            $this->addBreadcrumb($c['title'], href_to($ctype['name'], $c['slug']));
        }
    }

    if ($ctype['options']['item_on']){
        $this->addBreadcrumb($album['title'], href_to($ctype['name'], $album['slug']) . '.html');
    }

    if (cmsUser::isAllowed($ctype['name'], 'edit', 'all') ||
       (cmsUser::isAllowed($ctype['name'], 'edit', 'own') && $album['user_id'] == $user->id)){
        $this->addToolButton(array(
            'class' => 'edit',
            'title' => sprintf(LANG_CONTENT_EDIT_ITEM, $ctype['labels']['create']),
            'href'  => href_to($ctype['name'], 'edit', $album['id'])
        ));
   }

    if (cmsUser::isAllowed($ctype['name'], 'edit', 'all') ||
       (cmsUser::isAllowed($ctype['name'], 'edit', 'own') && $album['user_id'] == $user->id)){
        $this->addToolButton(array(
            'class' => 'delete',
            'title' => sprintf(LANG_CONTENT_DELETE_ITEM, $ctype['labels']['create']),
            'href'  => href_to($ctype['name'], 'delete', $album['id']),
            'onclick' => "if(!confirm('".sprintf(LANG_CONTENT_DELETE_ITEM_CONFIRM, $ctype['labels']['create'])."')){ return false; }"
        ));
   }

    $this->addBreadcrumb($photo['title']);

?>

<h1><?php echo $photo['title']; ?></h1>

<div id="album-photo-item" class="content_item">

    <div class="image">
        <?php echo html_image($photo['image'], 'big', $photo['title']); ?>
    </div>

    <div id="album-nav">
        <div class="arrow arr-prev"><a href="javascript:"></a></div>
        <div id="photos-slider">
            <ul>
                <?php $index = 0; ?>
                <?php foreach($photos as $thumb) { ?>
                    <li <?php if ($thumb['id'] == $photo['id']) { ?>class="active"<?php } ?>>
                        <a href="<?php echo $this->href_to('view', $thumb['id']); ?>" title="<?php echo $thumb['title']; ?>">
                            <?php echo html_image($thumb['image'], 'small'); ?>
                        </a>
                    </li>
                    <?php if ($thumb['id'] == $photo['id']) { $active_index = $index; } else { $index++; } ?>
                <?php } ?>
            </ul>
        </div>
        <div class="arrow arr-next"><a href="javascript:"></a></div>
    </div>

    <div class="info_bar">
        <?php if ($ctype['is_rating']){ ?>
            <div class="bar_item bi_rating">
                <?php echo $photo['rating_widget']; ?>
            </div>
        <?php } ?>
        <div class="bar_item bi_date_pub" title="<?php echo LANG_DATE_PUB; ?>">
            <?php echo html_date_time($photo['date_pub']); ?>
        </div>
        <div class="bar_item bi_user" title="<?php echo LANG_AUTHOR ?>">
            <a href="<?php echo href_to('users', $photo['user']['id']) ?>"><?php echo $photo['user']['nickname']; ?></a>
        </div>
    </div>

</div>

<?php
    $html = cmsEventsManager::hook("photos_item_html", $photo, false);
    if ($html) { echo $html; }
?>

<?php if ($ctype['is_comments']){ ?>
    <?php echo $photo['comments_widget']; ?>
<?php } ?>

<script>

    var li_w = 78;
    var li_in_frame = <?php echo sizeof($photos) > 7 ? 7 : sizeof($photos); ?>;
    var li_count = <?php echo sizeof($photos); ?>;
    var slider_w = li_w * li_in_frame;
    var left_li_offset = 3;
    var slide_left = <?php echo $active_index; ?> * li_w;
    var arrows_w = 32 + 32;

    var min_left = left_li_offset*li_w;
    var max_left = (li_w*li_count - slider_w);

    icms.photos.init = true;

</script>