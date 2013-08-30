<div class="widget_content_tree">

    <ul>

        <?php $last_level = 0; ?>

        <?php foreach($cats as $id=>$item){ ?>

            <?php
                $is_active = ($item['slug'] === $slug) || (!$item['slug'] && !$slug);
                if (!isset($item['ns_level'])) { $item['ns_level'] = 1; }
                $item['childs_count'] = ($item['ns_right'] - $item['ns_left']) > 1;
                $url = href_to($ctype_name, $item['slug']);
            ?>

            <?php for ($i=0; $i<($last_level - $item['ns_level']); $i++) { ?>
                </li></ul>
            <?php } ?>

            <?php if ($item['ns_level'] <= $last_level) { ?>
                </li>
            <?php } ?>

            <?php
                $css_classes = array();
                if ($is_active) { $css_classes[] = 'active'; }
                if ($item['childs_count']) { $css_classes[] = 'folder'; }
                $css_classes = $css_classes ? implode(' ', $css_classes) : false;
            ?>

            <li <?php if ($css_classes) { ?>class="<?php echo $css_classes; ?>"<?php } ?>>

                <a class="item" href="<?php echo $url; ?>">
                    <?php html($item['title']); ?>
                </a>

                <?php if ($item['childs_count']) { ?><ul><?php } ?>

            <?php $last_level = $item['ns_level']; ?>

        <?php } ?>

        <?php for ($i=0; $i<$last_level; $i++) { ?>
            </li></ul>
        <?php } ?>

</div>