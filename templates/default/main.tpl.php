<?php
    $config = cmsConfig::getInstance();
    $core = cmsCore::getInstance();
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php $this->title(); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width; initial-scale=1.0">
    <link type="text/css" rel="stylesheet" href="<?php echo $config->root; ?>templates/<?php echo $this->name; ?>/css/theme-text.css">
    <link type="text/css" rel="stylesheet" href="<?php echo $config->root; ?>templates/<?php echo $this->name; ?>/css/theme-layout.css">
    <link type="text/css" rel="stylesheet" href="<?php echo $config->root; ?>templates/<?php echo $this->name; ?>/css/theme-gui.css">
    <link type="text/css" rel="stylesheet" href="<?php echo $config->root; ?>templates/<?php echo $this->name; ?>/css/theme-widgets.css">
    <link type="text/css" rel="stylesheet" href="<?php echo $config->root; ?>templates/<?php echo $this->name; ?>/css/theme-content.css">
    <link type="text/css" rel="stylesheet" href="<?php echo $config->root; ?>templates/<?php echo $this->name; ?>/css/theme-modal.css">
	<script type="text/javascript" src="<?php echo $config->root; ?>templates/<?php echo $this->name; ?>/js/jquery.js"></script>
	<script type="text/javascript" src="<?php echo $config->root; ?>templates/<?php echo $this->name; ?>/js/jquery-modal.js"></script>
	<script type="text/javascript" src="<?php echo $config->root; ?>templates/<?php echo $this->name; ?>/js/core.js"></script>
	<script type="text/javascript" src="<?php echo $config->root; ?>templates/<?php echo $this->name; ?>/js/modal.js"></script>
	<script type="text/javascript" src="<?php echo $config->root; ?>templates/<?php echo $this->name; ?>/js/messages.js"></script>
    <!--[if lt IE 9]>
        <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
    <![endif]-->
    <?php $this->head(); ?>
    <style><?php include('options.css.php'); ?></style>
</head>
<body><div id="background">

    <div id="wrapper">

        <header>
            <div id="logo"><a href="<?php echo href_to_home(); ?>"></a></div>
            <?php $this->widgets('header', false, 'wrapper_plain'); ?>
        </header>

        <?php if($this->hasWidgetsOn('top')) { ?>
            <nav>
                <?php $this->widgets('top', false, 'wrapper_plain'); ?>
            </nav>
        <?php } ?>

        <div id="body">

            <?php
                $is_sidebar = $this->hasWidgetsOn('right-top', 'right-center', 'right-bottom');
                $section_width = $is_sidebar ? '710px' : '100%';
            ?>

            <?php if ($this->isBreadcrumbs()){ ?>
                <div id="breadcrumbs">
                    <?php $this->breadcrumbs(array('strip_last'=>false)); ?>
                </div>
            <?php } ?>

            <?php
                $messages = cmsUser::getSessionMessages();
                if ($messages){
                    ?>
                    <div class="sess_messages">
                        <?php
                            foreach($messages as $message){
                                echo $message;
                            }
                        ?>
                    </div>
                    <?php
                }
            ?>

            <section style="width:<?php echo $section_width; ?>">

                <?php $this->widgets('left-top'); ?>

                <?php if ($this->isBody()){ ?>
                    <article>
                        <?php $this->body(); ?>
                    </article>
                <?php } ?>

                <?php $this->widgets('left-bottom'); ?>

            </section>

            <aside>
                <?php $this->widgets('right-top'); ?>

                <?php $this->widgets('right-center'); ?>

                <?php $this->widgets('right-bottom'); ?>
            </aside>

        </div>

        <?php if ($config->debug){ ?>
            <div id="sql_debug" style="display:none">
                <div id="sql_queries">
                    <?php foreach($core->db->query_list as $sql) { ?>
                        <div class="query">
                            <div class="src"><?php echo $sql['src']; ?></div>
                            <?php echo nl2br($sql['sql']); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <footer>
            <ul>
                <?php if ($this->options['owner_name']) { ?>
                <li id="copyright">
                    <a href="<?php echo $this->options['owner_url'] ? $this->options['owner_url'] : href_to_home(); ?>"><?php html($this->options['owner_name']) ;?></a>
                    &copy;
                    <?php echo $this->options['owner_year'] ? $this->options['owner_year'] : date('Y'); ?>
                </li>
                <?php } ?>
                <li id="info">
                    <span class="item">
                        <small><?php echo LANG_POWERED_BY_INSTANTCMS; ?></small>
                    </span>
                    <?php if ($config->debug){ ?>
                        <span class="item">
                            SQL: <a href="#sql_debug" class="ajax-modal"><?php echo html_spellcount($core->db->query_count, 'query|queries|queries'); ?></a>
                        </span>
                        <span class="item">
                            Cache: <?php echo html_spellcount(cmsCache::getInstance()->query_count, 'query|queries|queries'); ?></a>
                        </span>
                        <span class="item">
                            Mem: <?php echo round(memory_get_usage()/1024/1024, 2); ?> Mb
                        </span>
                    <?php } ?>
                </li>
                <li id="nav">
                    <?php $this->widgets('footer', false, 'wrapper_plain'); ?>
                </li>
            </ul>
        </footer>

    </div>

</div></body>
</html>
