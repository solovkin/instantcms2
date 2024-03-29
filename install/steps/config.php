<?php

function step($is_submit){

    $root = $_SESSION['install']['paths']['root'];

    $path = $_SERVER['DOCUMENT_ROOT'] . $root . 'system/config';
    $file = 'config.php';

    if ($is_submit){
        return create_config($path, $file);
    }

    $result = array(
        'html' => render('step_config', array(
            'path' => $path,
            'file' => $file,
        ))
    );

    return $result;

}

function create_config($path, $file){

    if (!is_writable($path)){
        return array(
            'error' => true,
            'message' => LANG_CONFIG_NOT_WRITABLE
        );
    }

    $file = $path . '/' . $file;

    $config = array(
        'root'					=> $_SESSION['install']['paths']['root'],
        'host'					=> $_SESSION['install']['hosts']['root'],
        'upload_root'			=> $_SESSION['install']['paths']['upload'],
        'upload_host'			=> $_SESSION['install']['hosts']['upload'],
        'cache_root'			=> $_SESSION['install']['paths']['cache'],
        'sitename'				=> LANG_CFG_SITENAME,
        'hometitle'				=> LANG_CFG_HOMETITLE,
        'date_format'			=> LANG_CFG_DATE_FORMAT,
        'date_format_js'		=> LANG_CFG_DATE_FORMAT_JS,
        'time_zone'				=> LANG_CFG_TIME_ZONE,
        'template'				=> 'default',
        'db_host'				=> $_SESSION['install']['db']['host'],
        'db_base'				=> $_SESSION['install']['db']['base'],
        'db_user'				=> $_SESSION['install']['db']['user'],
        'db_pass'				=> $_SESSION['install']['db']['pass'],
        'db_prefix'				=> $_SESSION['install']['db']['prefix'],
        'db_users_table'		=> "{$_SESSION['install']['db']['users_table']}",
        'language'				=> LANG,
        'metakeys'				=> LANG_CFG_METAKEYS,
        'metadesc'				=> LANG_CFG_METADESC,
        'ct_autoload'			=> 'frontpage',
        'ct_default'			=> 'content',
        'frontpage'             => 'none',
        'debug'					=> 0,
        'emulate_lag'			=> '',
        'cache_enabled'			=> 0,
        'cache_method'			=> 'files',
        'cache_ttl'				=> 300,
        'cache_host'			=> 'localhost',
        'cache_port'			=> 11211,
        'min_html'				=> 0,
        'mail_transport'		=> 'mail',
        'mail_from'				=> 'noreply@example.com',
        'mail_smtp_server'		=> 'smtp.example.com',
        'mail_smtp_port'		=> 25,
        'mail_smtp_auth'		=> 1,
        'mail_smtp_user'		=> 'user@example.com',
        'mail_smtp_pass'		=> '',
    );

    write_config($file, $config);

    return array(
        'error' => false,
    );

}

function write_config($file, $config){

    $dump = "<?php\n" .
            "return array(\n\n";

    foreach($config as $key=>$value){

        if (!is_numeric($value)) { $value = "'{$value}'"; }

        $tabs = 7 - ceil((mb_strlen($key)+3)/4);

        $dump .= "\t'{$key}'";
        $dump .= str_repeat("\t", $tabs);
        $dump .= "=> $value,\n";

    }

    $dump .= "\n);\n";

    return @file_put_contents($file, $dump);

}
