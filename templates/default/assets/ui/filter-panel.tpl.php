<div class="filter-panel gui-panel <?php echo $css_prefix;?>-filter">
    <?php if(!$filters){ ?>
        <div class="filter-link">
            <a href="javascript:toggleFilter()"><span><?php echo LANG_SHOW_FILTER; ?></span></a>
        </div>
    <?php } ?>
    <div class="filter-container" <?php if(!$filters){ ?>style="display:none"<?php } ?>>
        <form action="<?php echo is_array($page_url) ? $page_url['base'] : $page_url; ?>" method="post">
            <?php echo html_input('hidden', 'page', 1); ?>
            <div class="fields">
                <?php $fields_count = 0; ?>
                <?php foreach($fields as $name => $field){ ?>
                    <?php if (!$field['is_in_filter']){ continue; } ?>
                    <?php $fields_count++; ?>
                    <div class="field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?>">
                        <div class="title"><?php echo $field['title']; ?></div>
                        <div class="value">
                            <?php $value = isset($filters[$name]) ? $filters[$name] : null; ?>
                            <?php echo $field['handler']->getFilterInput($value); ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <?php if ($fields_count) { ?>
                <div class="buttons">
                    <?php echo html_submit(LANG_FILTER_APPLY); ?>
                    <?php if (sizeof($filters)){ ?>
                        <div class="link">
                            <a href="<?php echo is_array($page_url) ? $page_url['base'] : $page_url; ?>"><?php echo LANG_CANCEL; ?></a>
                        </div>
                        <div class="link">
                            # <a href="<?php echo is_array($page_url) ? $page_url['base'] : $page_url; ?>?<?php echo http_build_query($filters); ?>"><?php echo LANG_FILTER_URL; ?></a>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </form>
    </div>
</div>