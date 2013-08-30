<?php
    $config = cmsConfig::getInstance();
    $user = cmsUser::getInstance();
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php $this->title(); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link type="text/css" rel="stylesheet" href="<?php echo $config->root; ?>templates/<?php echo $this->name; ?>/css/theme-modal.css" />
    <script type="text/javascript" src="<?php echo $config->root; ?>templates/<?php echo $this->name; ?>/js/jquery.js"></script>
    <script type="text/javascript" src="<?php echo $config->root; ?>templates/<?php echo $this->name; ?>/js/jquery-modal.js"></script>
	<script type="text/javascript" src="<?php echo $config->root; ?>templates/<?php echo $this->name; ?>/js/core.js"></script>
	<script type="text/javascript" src="<?php echo $config->root; ?>templates/<?php echo $this->name; ?>/js/modal.js"></script>
    <?php $this->head(); ?>
</head>

<body>

    <div id="wrapper">

        <div id="cp_top_line">
            <div id="links">
                <a href="<?php echo href_to('users', $user->id); ?>" class="user"><?php echo $user->nickname; ?></a>
                <a href="<?php echo LANG_HELP_URL; ?>"><?php echo LANG_HELP; ?></a>
                <a href="<?php echo href_to_home(); ?>"><?php echo LANG_CP_BACK_TO_SITE; ?></a>
                <a href="<?php echo href_to('auth', 'logout'); ?>" class="logout"><?php echo LANG_LOG_OUT; ?></a>
            </div>
        </div>

        <div id="cp_header">
            <div id="logo"><a href="<?php echo href_to('admin'); ?>"></a></div>
            <div id="menu"><?php $this->menu('cp_main'); ?></div>
        </div>

        <div id="cp_pathway">
            <?php $this->breadcrumbs(array('home_url' => href_to('admin'), 'strip_last'=>false, 'separator'=>'<div class="sep"></div>')); ?>
        </div>

        <div id="cp_body">

                <!-- Сообщения сессии -->
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

                <!-- Вывод тела -->
                <?php $this->body(); ?>

                <div class="pad"></div>

        </div>

    </div>

    <div id="cp_footer">
        <div class="container">
            <a href="http://www.instantcms.ru/">InstantCMS</a> v<?php echo cmsCore::getVersion(); ?> &mdash;
            &copy; <a href="http://www.instantsoft.ru/">InstantSoft</a> 2013 &mdash;
            <a href="<?php echo href_to('admin', 'credits'); ?>"><?php echo LANG_CP_3RDPARTY_CREDITS; ?></a>
        </div>
    </div>

    <script>

        function fitLayout(){
            var h1 = $('#cp_body h1').offset().top + $('#cp_body h1').height();
            var h2 = $('#cp_footer').offset().top;
            $('table.layout').height(h2 - h1 + 2);
            $('table.layout').width( $('#cp_body').width() + 40 );
        }

        $(document).ready(function(){
            fitLayout();
        });

        $(window).resize(function(){
            fitLayout();
        });

    </script>

</body>
</html>
