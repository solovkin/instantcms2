<?php if ($fields['title']['is_in_item']){ ?>
    <h1>
        <?php if ($item['parent_id']){ ?>
            <div class="parent_title">
                <a href="<?php echo href_to($item['parent_url']); ?>"><?php html($item['parent_title']); ?></a> &rarr;
            </div>
        <?php } ?>
        <?php html($item['title']); ?>
        <?php if ($item['is_private']) { ?>
            <span class="is_private" title="<?php html(LANG_PRIVACY_PRIVATE); ?>"></span>
        <?php } ?>
    </h1>
    <?php unset($fields['title']); ?>
<?php } ?>

<div class="content_item <?php echo $ctype['name']; ?>_item">

    <?php foreach($fields as $name=>$field){ ?>

        <?php if (!$field['is_in_item']) { continue; } ?>
        <?php if ($field['is_system']) { continue; } ?>
        <?php if (empty($item[$field['name']])) { continue; } ?>

        <?php
            if (!isset($field['options']['label_in_item'])) {
                $label_pos = 'none';
            } else {
                $label_pos = $field['options']['label_in_item'];
            }
        ?>

        <div class="field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?>">

            <?php if ($label_pos != 'none'){ ?>
                <div class="title_<?php echo $label_pos; ?>"><?php html($field['title']); ?>: </div>
            <?php } ?>

            <div class="value">

                <?php
                    echo $field['html'];
                ?>

            </div>

        </div>

    <?php } ?>


    <?php
        $hooks_html = cmsEventsManager::hookAll("content_{$ctype['name']}_item_html", $item);
        if ($hooks_html) { echo html_each($hooks_html); }
    ?>

    <?php
        $is_tags = $ctype['is_tags'] &&
                   !empty($ctype['options']['is_tags_in_item']) &&
                   $item['tags'];
    ?>

    <?php if ($is_tags){ ?>
        <div class="tags_bar">
            <?php echo html_tags_bar($item['tags']); ?>
        </div>
    <?php } ?>

    <?php
        $show_bar = $ctype['is_rating'] ||
                    $fields['date_pub']['is_in_item'] ||
                    $fields['user']['is_in_item'] ||
                    !$item['is_approved'];
    ?>

    <?php if ($show_bar){ ?>
        <div class="info_bar">
            <?php if ($ctype['is_rating']){ ?>
                <div class="bar_item bi_rating">
                    <?php echo $item['rating_widget']; ?>
                </div>
            <?php } ?>
            <?php if ($fields['date_pub']['is_in_item']){ ?>
                <div class="bar_item bi_date_pub" title="<?php html( $fields['date_pub']['title'] ); ?>">
                    <?php echo $fields['date_pub']['html']; ?>
                </div>
            <?php } ?>
            <?php if ($fields['user']['is_in_item']){ ?>
                <div class="bar_item bi_user" title="<?php html( $fields['user']['title'] ); ?>">
                    <?php echo $fields['user']['html']; ?>
                </div>
            <?php } ?>
            <?php if (!$item['is_approved']){ ?>
                <div class="bar_item bi_not_approved">
                    <?php echo LANG_CONTENT_NOT_APPROVED; ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

</div>
