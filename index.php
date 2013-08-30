<?php

	session_start();

	define('VALID_RUN', true);

	// Устанавливаем кодировку
	header("Content-type:text/html; charset=utf-8");
    header('X-Powered-By: InstantCMS');

    require_once "bootstrap.php";
    
    if ($config->emulate_lag) { usleep(350000); }

    // Инициализируем шаблонизатор
    $template = cmsTemplate::getInstance();

    // Запускаем кеш
    cmsCache::getInstance()->start();

    cmsEventsManager::hook('engine_start');

    //Запускаем роутинг и контроллер
    $core->route($_SERVER['REQUEST_URI']);
	$core->runController();
    $core->runWidgets();

    //Выводим готовую страницу
    $template->renderPage();

    cmsEventsManager::hook('engine_stop');

    // Останавливаем кеш
    cmsCache::getInstance()->stop();
