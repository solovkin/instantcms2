<?php $this->addJS( $this->getJavascriptFileName('fileuploader') ); ?>
<?php $this->addJS( $this->getJavascriptFileName('images-upload') ); ?>

<?php $config = cmsConfig::getInstance(); ?>

<div id="widget_image_<?php echo $name; ?>" class="widget_image_multi">

    <div class="data" style="display:none">
        <?php if ($images){ ?>
            <?php foreach($images as $idx => $paths){ ?>
                <?php foreach($paths as $path_name => $path){ ?>
                    <input type="hidden" name="<?php echo $name; ?>[<?php echo $idx; ?>][<?php echo $path_name; ?>]" value="<?php echo $path; ?>" rel="<?php echo $idx; ?>"/>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </div>

    <div class="previews_list">
        <?php if ($images){ ?>
            <?php foreach($images as $idx => $paths){ ?>
                <div class="preview block" rel="<?php echo $idx; ?>">
                    <img src="<?php echo $config->upload_host . '/' . $paths['small']; ?>" border="0" />
                    <a href="javascript:" onclick="icms.images.removeOne('<?php echo $name; ?>', <?php echo $idx; ?>)"><?php echo LANG_DELETE; ?></a>
                </div>
            <?php } ?>
        <?php } ?>
    </div>

    <div class="preview_template block" style="display:none">
        <img src="" border="0" />
        <a href="javascript:"><?php echo LANG_DELETE; ?></a>
    </div>

    <div id="file-uploader-<?php echo $name; ?>"></div>

    <script>

        <?php echo $this->getLangJS('LANG_SELECT_UPLOAD'); ?>
        <?php echo $this->getLangJS('LANG_DROP_TO_UPLOAD'); ?>
        <?php echo $this->getLangJS('LANG_CANCEL'); ?>
        <?php echo $this->getLangJS('LANG_ERROR'); ?>

        icms.images.createUploader('<?php echo $name; ?>', '<?php echo $this->href_to('upload'); ?>/<?php echo $name; ?>', true);

    </script>

</div>
