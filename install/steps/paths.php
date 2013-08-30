<?php

function step($is_submit){

    if ($is_submit){
        return check_writables();
    }

    $doc_root = str_replace(DS, '/', $_SERVER['DOCUMENT_ROOT']);
    $root = str_replace($doc_root, '', str_replace(DS, '/', dirname(PATH)));

    $paths = array(
        'root' => $root . '/',
        'upload' => $root . '/' . 'upload' . '/',
        'cache' => $root . '/' . 'cache' . '/'
    );

    $hosts = array(
        'root' => 'http://' . $_SERVER['HTTP_HOST'] . $root,
        'upload' => 'http://' . $_SERVER['HTTP_HOST'] . $root . '/upload',
    );

    $result = array(
        'html' => render('step_paths', array(
            'doc_root' => $doc_root,
            'root' => $root,
            'is_subfolder' => ($root != ''),
            'paths' => $paths,
            'hosts' => $hosts
        ))
    );

    return $result;

}

function check_writables(){

    $error = false;
    $message = '';

    $doc_root = $_SERVER['DOCUMENT_ROOT'];
    $paths = $_POST['paths'];
    $hosts = $_POST['hosts'];

    $upload = rtrim($doc_root . $paths['upload'], '/');
    $cache = rtrim($doc_root . $paths['cache'], '/');

    if (!is_writable($upload)){
        $error = true;
        $message = LANG_PATHS_UPLOAD_PATH . ' '. LANG_PATHS_NOT_WRITABLE . "\n" . LANG_PATHS_WRITABLE_HINT;
    } else

    if (!is_writable($cache)){
        $error = true;
        $message = LANG_PATHS_CACHE_PATH . ' '. LANG_PATHS_NOT_WRITABLE . "\n" . LANG_PATHS_WRITABLE_HINT;
    }

    if (!$error){
        $_SESSION['install']['paths'] = $paths;
        $_SESSION['install']['hosts'] = $hosts;
    }

    return array(
        'error' => $error,
        'message' => $message
    );

}
