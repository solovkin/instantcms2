<h1><?php echo LANG_STEP_PATHS; ?></h1>

<p><?php printf(LANG_PATHS_ROOT_INFO, $doc_root . $root); ?></p>

<form id="step-form">

    <fieldset>

        <legend><?php echo LANG_PATHS_ROOT; ?></legend>

        <div class="field">
            <label><?php echo LANG_PATHS_ROOT_PATH; ?></label>
            <input type="text" class="input input-icon icon-folder" name="paths[root]" value="<?php echo $paths['root']; ?>" />
        </div>

        <div class="field">
            <label><?php echo LANG_PATHS_ROOT_HOST; ?></label>
            <input type="text" class="input input-icon icon-url" name="hosts[root]" value="<?php echo $hosts['root']; ?>" />
        </div>

    </fieldset>

    <fieldset>

        <legend><?php echo LANG_PATHS_UPLOAD; ?></legend>

        <div class="field">
            <div class="hint"><?php echo LANG_PATHS_MUST_WRITABLE; ?></div>
            <label><?php echo LANG_PATHS_UPLOAD_PATH; ?></label>
            <input type="text" class="input input-icon icon-folder" name="paths[upload]" value="<?php echo $paths['upload']; ?>" />
        </div>

        <div class="field">
            <label><?php echo LANG_PATHS_UPLOAD_HOST; ?></label>
            <input type="text" class="input input-icon icon-url" name="hosts[upload]" value="<?php echo $hosts['upload']; ?>" />
        </div>

    </fieldset>

    <fieldset>

        <legend><?php echo LANG_PATHS_CACHE; ?></legend>

        <div class="field">
            <div class="hint"><?php echo LANG_PATHS_MUST_WRITABLE; ?></div>
            <label><?php echo LANG_PATHS_CACHE_PATH; ?></label>
            <input type="text" class="input input-icon icon-folder" name="paths[cache]" value="<?php echo $paths['cache']; ?>" />
        </div>

    </fieldset>

</form>

<?php if ($is_subfolder){ ?>
    <p class="warning">
        <?php echo LANG_PATHS_HTACCESS_INFO ?>
    </p>
<?php } ?>

<p><?php echo LANG_PATHS_CHANGE_INFO ?></p>

<div class="buttons">
    <input type="button" name="next" id="btn-next" value="<?php echo LANG_NEXT; ?>" onclick="submitStep()" />
</div>

