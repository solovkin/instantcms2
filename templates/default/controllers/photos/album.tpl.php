<?php $this->addJS( $this->getJavascriptFileName('fileuploader') ); ?>
<?php $this->addJS( $this->getJavascriptFileName('photos') ); ?>

<div id="album-photos-list">

    <?php if (is_array($photos)){ ?>
        <?php foreach($photos as $photo){ ?>

            <div class="photo">
                <div class="image">
                    <a href="<?php echo $this->href_to('view', $photo['id']); ?>" title="<?php html($photo['title']); ?>">
                        <?php echo html_image($photo['image'], 'normal', $photo['title']); ?>
                    </a>
                </div>
                <div class="info">
                    <div class="rating <?php echo html_signed_class($photo['rating']); ?>">
                        <?php echo html_signed_num($photo['rating']); ?>
                    </div>
                    <div class="comments">
                        <span><?php echo $photo['comments']; ?></span>
                    </div>
                </div>
            </div>

        <?php } ?>
    <?php } ?>

</div>

<?php if ($perpage < $total) { ?>
    <?php echo html_pagebar($page, $perpage, $total, $page_url); ?>
<?php } ?>
