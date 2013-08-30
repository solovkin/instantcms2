#wrapper {
    width: <?php echo $this->options['layout_type']=='fixed' ? $this->options['layout_min_width'] . 'px' : $this->options['layout_width'] . '%'; ?> !important;
    <?php if ($this->options['layout_type']=='adaptive'){ ?>
        min-width: <?php echo $this->options['layout_min_width']; ?>px; !important;
    <?php } ?>
}
<?php if (!empty($this->options['logo'])){ ?>
header #logo a {
    background-image: url("<?php echo $config->upload_root . $this->options['logo']['original']; ?>") !important;
}
<?php } ?>
section {
    float: <?php echo $this->options['aside_pos']=='left' ? 'right' : 'left'; ?> !important;
}
aside {
    float: <?php echo $this->options['aside_pos']=='left' ? 'left' : 'right'; ?> !important;
}
aside .menu li ul {
    <?php echo $this->options['aside_pos']=='left' ? 'right' : 'left'; ?>: auto !important;
    <?php if ($this->options['aside_pos']=='left'){ ?>left: 210px;<?php } ?>
}
@media screen and (max-width: 980px) {
    #wrapper { width: 98% !important; min-width: 0 !important; }
}
