<div class="content_item <?php echo $ctype['name']; ?>_item">

    <?php foreach($fields as $field){ ?>

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
                <div class="title_<?php echo $label_pos; ?>"><?php echo $field['title']; ?>: </div>
            <?php } ?>

            <div class="value">

                <?php
                    echo $field['handler']->parse( $item[$field['name']] );
                ?>

            </div>

        </div>

    <?php } ?>

    <?php
        $show_bar = $ctype['is_rating'] ||
                    $fields['date_pub']['is_in_item'] ||
                    $fields['user']['is_in_item'];
    ?>

    <?php if ($show_bar){ ?>
        <div class="info_bar">
            <?php if ($ctype['is_rating']){ ?>
                <div class="bar_item bi_rating">
                    <?php echo $item['rating_widget']; ?>
                </div>
            <?php } ?>
            <?php if ($fields['date_pub']['is_in_item']){ ?>
                <div class="bar_item bi_date_pub" title="<?php echo $fields['date_pub']['title']; ?>">
                    <?php echo $fields['date_pub']['handler']->parse( $item['date_pub'] ); ?>
                </div>
            <?php } ?>
            <?php if ($fields['user']['is_in_item']){ ?>
                <div class="bar_item bi_user" title="<?php echo $fields['user']['title']; ?>">
                    <?php echo $fields['user']['handler']->parse( $item['user'] ); ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

</div>