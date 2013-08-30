<!DOCTYPE html>
<html>
<head>
	<title><?php echo LANG_PAGE_TITLE; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link type="text/css" rel="stylesheet" href="css/styles.css">
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/install.js"></script>
</head>
<body>

    <div id="layout">

        <div id="header" class="section">
            <div class="logo">
                <span><?php echo LANG_INSTALLATION_WIZARD; ?></span>
                <div class="langs">
                    <?php foreach($langs as $id){ ?>
                        <a <?php if ($id==$lang) { ?>class="selected"<?php } ?> style="background-image:url('languages/<?php echo $id; ?>/flag.png')" href="?lang=<?php echo $id; ?>"><?php echo mb_strtoupper($id); ?></a>
                    <?php } ?>
                </div>
            </div>
        </div>

        <table id="main" class="section">
            <tr>

                <td id="sidebar" valign="top">
                    <ul id="steps">
                        <?php foreach($steps as $num => $step) { ?>
                            <li id="<?php echo $step['id']; ?>" <?php if($num==$current_step) { ?>class="active"<?php } ?>>
                                <?php echo $num+1; ?>. <?php echo $step['title']; ?>
                            </li>
                        <?php } ?>
                    </ul>
                </td>

                <td id="body" valign="top">
                    <?php echo $step_html; ?>
                </td>

            </tr>
        </table>

        <div id="footer" class="section">
            <div id="copyright">
                <a href="http://www.instantsoft.ru">InstantSoft</a> &copy; 2013
            </div>
            <div id="version">
                2.0.beta
            </div>
        </div>

    </div>

    <script>

        var current_step = <?php echo $current_step; ?>;

        <?php if (!$is_lang_selected) { ?>
            $(document).ready(function(){
                var l = $('.langs');
                var speed = 200;
                l.fadeOut(speed, function(){
                    l.fadeIn(speed, function(){
                        l.fadeOut(speed, function(){
                            l.fadeIn(speed, function(){
                                l.fadeOut(speed, function(){
                                    l.fadeIn(speed);
                                });
                            });
                        });
                    });
                });
            })
        <?php } ?>

    </script>

</body>
</html>
