<?php
    if( $ctype['options']['list_show_filter'] ) {
        $this->renderAsset('ui/filter-panel', array(
            'css_prefix' => $ctype['name'],
            'page_url' => $page_url,
            'fields' => $fields,
            'filters' => $filters,
        ));
    }
?>

<?php if ($items){ ?>

    <div class="content_list <?php echo $ctype['name']; ?>_list">

        <?php foreach($items as $item){ ?>

            <div class="content_list_item <?php echo $ctype['name']; ?>_list_item">

                <?php foreach($fields as $field){ ?>

                    <?php if (empty($item[$field['name']])) { continue; } ?>
                    <?php if ($field['is_system']) { continue; } ?>
                    <?php if (!$field['is_in_list']) { continue; } ?>

                    <?php if ($field['groups_read'] && !$user->isInGroups($field['groups_read'])) { continue; } ?>

                    <?php
                        if (!isset($field['options']['label_in_list'])) {
                            $label_pos = 'none';
                        } else {
                            $label_pos = $field['options']['label_in_list'];
                        }
                    ?>

                    <div class="field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?>">

                        <?php if ($label_pos != 'none'){ ?>
                            <div class="title_<?php echo $label_pos; ?>"><?php echo $field['title'] . ($label_pos=='left' ? ': ' : ''); ?></div>
                        <?php } ?>

                        <div class="value">
                            <?php if ($field['name'] == 'title' && $ctype['options']['item_on']){ ?>

                                <?php if ($item['parent_id']){ ?>
                                    <a class="parent_title" href="<?php echo href_to($item['parent_url']); ?>"><?php echo htmlspecialchars($item['parent_title']); ?></a>
                                    &rarr;
                                <?php } ?>

                                <a href="<?php echo href_to($ctype['name'], $item['slug'].'.html'); ?>"><?php echo htmlspecialchars($item[$field['name']]); ?></a>

                            <?php } else { ?>
                                <?php echo $field['handler']->parseTeaser($item[$field['name']]); ?>
                            <?php } ?>
                        </div>

                    </div>

                <?php } ?>

                <?php
                    $is_tags = $ctype['is_tags'] &&
                            !empty($ctype['options']['is_tags_in_list']) &&
                            $item['tags'];
                ?>

                <?php if ($is_tags){ ?>
                    <div class="tags_bar">
                        <?php echo html_tags_bar($item['tags']); ?>
                    </div>
                <?php } ?>

                <?php
                    $show_bar = $ctype['is_rating'] ||
                                $fields['date_pub']['is_in_list'] ||
                                $fields['user']['is_in_list'] ||
                                !$item['is_approved'];
                ?>

                <?php if ($show_bar){ ?>
                    <div class="info_bar">
                        <?php if ($ctype['is_rating']){ ?>
                            <div class="bar_item bi_rating">
                                <?php echo $item['rating_widget']; ?>
                            </div>
                        <?php } ?>
                        <?php if ($fields['date_pub']['is_in_list']){ ?>
                            <div class="bar_item bi_date_pub" title="<?php echo $fields['date_pub']['title']; ?>">
                                <?php echo $fields['date_pub']['handler']->parse( $item['date_pub'] ); ?>
                            </div>
                        <?php } ?>
                        <?php if ($fields['user']['is_in_list']){ ?>
                            <div class="bar_item bi_user" title="<?php echo $fields['user']['title']; ?>">
                                <?php echo $fields['user']['handler']->parse( $item['user'] ); ?>
                            </div>
                        <?php } ?>
                        <?php if ($ctype['is_comments']){ ?>
                            <div class="bar_item bi_comments">
                                <a href="<?php echo href_to($ctype['name'], $item['slug'].'.html'); ?>#comments" title="<?php echo LANG_COMMENTS; ?>"><?php echo intval($item['comments']); ?></a>
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

        <?php } ?>

    </div>

    <?php if ($perpage < $total) { ?>
        <?php echo html_pagebar($page, $perpage, $total, $page_url, $filters); ?>
    <?php } ?>

<?php } else { echo LANG_LIST_EMPTY; } ?>