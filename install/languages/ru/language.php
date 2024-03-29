<?php

    define('LANG_PAGE_TITLE',               'Установка InstantCMS');
    define('LANG_INSTALLATION_WIZARD',      'Мастер установки');
    define('LANG_NEXT',                     'Далее &rarr;');

    define('LANG_MANUAL',                   '<a href="http://docs.instantcms.ru/manual/install" target="_blank">Инструкция по установке</a>');

    define('LANG_BETA_WARNING',             'Данная сборка является бета-версией и предназначена только для тестирования.');

    define('LANG_LANGUAGE_SELECT_RU',       'Пожалуйста, выберите язык');
    define('LANG_LANGUAGE_SELECT_EN',       'Please, select a language');

    define('LANG_STEP_LANGUAGE',            'Выбор языка');
    define('LANG_STEP_START',               'Вступление');
    define('LANG_STEP_LICENSE',             'Лицензия');
    define('LANG_STEP_PHP_CHECK',           'Проверка PHP');
    define('LANG_STEP_PATHS',               'Указание путей');
    define('LANG_STEP_DATABASE',            'База данных');
    define('LANG_STEP_ADMIN',               'Администратор');
    define('LANG_STEP_CONFIG',              'Конфигурация');
    define('LANG_STEP_CRON',                'Планировщик');
    define('LANG_STEP_FINISH',              'Завершение');

    define('LANG_STEP_START_1',             'Мастер установки InstantCMS проверит удовлетворяет ли ваш сервер системным требованиям.');
    define('LANG_STEP_START_2',             'В процессе работы мастер задаст несколько вопросов, необходимых для корректной установки и настройки InstantCMS.');
    define('LANG_STEP_START_3',             'Перед началом установки необходимо создать чистую базу данных MySQL в кодировке <b>utf8_general_ci</b>');

    define('LANG_LICENSE_AGREE',            'Я согласен с условиями лицензии');
    define('LANG_LICENSE_ERROR',            'Вы должны согласиться с условиями лицензии');
    define('LANG_LICENSE_NOTE',             'InstantCMS распространяется по лицензии <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GNU/GPL</a> версии 2.');
    define('LANG_LICENSE_ORIGINAL',         'Оригинал');
    define('LANG_LICENSE_TRANSLATION',      'Перевод');

    define('LANG_PHP_VERSION',              'Версия интерпретатора');
    define('LANG_PHP_VERSION_REQ',          'Требуется PHP 5.3 или выше');
    define('LANG_PHP_VERSION_DESC',         'Установленная версия');
    define('LANG_PHP_EXTENSIONS',           'Требуемые расширения');
    define('LANG_PHP_EXTENSIONS_REQ',       'Данные расширения необходимы для работы InstantCMS');
    define('LANG_PHP_EXTENSIONS_EXTRA',     'Рекомендуемые расширения');
    define('LANG_PHP_EXTENSIONS_EXTRA_REQ', 'Данные расширения не являются необходимыми, но без них будет недоступна часть функционала');
    define('LANG_PHP_EXT_INSTALLED',        'Установлено');
    define('LANG_PHP_EXT_NOT_INSTALLED',    'Не найдено');
    define('LANG_PHP_CHECK_ERROR',          'Вы не сможете продолжить установку до тех пор, пока условия отмеченные красным не будут исправлены.');
    define('LANG_PHP_CHECK_ERROR_HINT',     'Обратитесь в службу поддержки вашего хостинга с просьбой обеспечить необходимые условия');

    define('LANG_PATHS_ROOT_INFO',          'Все пути указываются относительно:<br/><b>%s</b>');
    define('LANG_PATHS_CHANGE_INFO',        'После установки пути можно будет изменить отредактировав файл конфигурации.<br/>Не забудьте сделать это при переносе сайта с локального сервера на хостинг!');
    define('LANG_PATHS_HTACCESS_INFO',      'Вы устанавливаете InstantCMS не в корневую папку сайта, поэтому необходимо изменить файл .htaccess. <a href="http://docs.instantcms.ru/manual/install?&#указание-путей" target="_blank">Инструкция</a>');
    define('LANG_PATHS_MUST_WRITABLE',      'Должна быть доступна для записи');
    define('LANG_PATHS_NOT_WRITABLE',       'не доступна для записи!');
    define('LANG_PATHS_WRITABLE_HINT',      'Выставьте правильные права на эту папку');

    define('LANG_PATHS_ROOT',               'Корень');
    define('LANG_PATHS_ROOT_PATH',          'Корневая папка');
    define('LANG_PATHS_ROOT_HOST',          'Корневой URL');
    define('LANG_PATHS_UPLOAD',             'Загрузки');
    define('LANG_PATHS_UPLOAD_PATH',        'Папка для загрузок');
    define('LANG_PATHS_UPLOAD_HOST',        'URL для загрузок');
    define('LANG_PATHS_CACHE',              'Кеш');
    define('LANG_PATHS_CACHE_PATH',         'Папка для кеша');

    define('LANG_DATABASE_INFO',            'Укажите реквизиты для подключения к базе данных MySQL');
    define('LANG_DATABASE_CHARSET_INFO',    'База данных должна быть в кодировке <b>utf8_general_ci</b>');
    define('LANG_DATABASE_HOST',            'Сервер MySQL');
    define('LANG_DATABASE_USER',            'Пользователь');
    define('LANG_DATABASE_PASS',            'Пароль');
    define('LANG_DATABASE_BASE',            'База данных');
    define('LANG_DATABASE_PREFIX',          'Префикс таблиц');
    define('LANG_DATABASE_USERS_TABLE',     'Таблица с пользователями');
    define('LANG_DATABASE_USERS_TABLE_NEW', 'Создать новую');
    define('LANG_DATABASE_USERS_TABLE_OLD', 'Использовать имеющуюся');

    define('LANG_DATABASE_CONNECT_ERROR',   "Ошибка подключения MySQL:\n\n%s");
    define('LANG_DATABASE_BASE_ERROR',      "Ошибка импорта базы данных\nПроверьте правильность реквизитов");

    define('LANG_ADMIN_EXTERNAL',           'Реквизиты администратора будут взяты из таблицы <b>%s</b>');
    define('LANG_ADMIN_INFO',               'Для создания главного администратора необходимо указать его реквизиты');
    define('LANG_ADMIN_EMAIL',              'E-mail администратора');
    define('LANG_ADMIN_PASS',               'Пароль администратора');
    define('LANG_ADMIN_PASS2',              'Пароль повторно');

    define('LANG_ADMIN_ERROR',              'Заполните все поля');
    define('LANG_ADMIN_EMAIL_ERROR',        'Указан некорректный адрес e-mail');
    define('LANG_ADMIN_PASS_ERROR',         'Пароли не совпадают');

    define('LANG_CONFIG_INFO',              'Сейчас будет создан файл конфигурации сайта.');
    define('LANG_CONFIG_PATH',              'Место расположения файла:');
    define('LANG_CONFIG_MUST_WRITABLE',     'Указанная папка должна быть доступна для записи.');
    define('LANG_CONFIG_AFTER',             'После создания файла конфигурации необходимо будет сделать эту папку (и находящиеся в ней файлы) недоступными для записи.');
    define('LANG_CONFIG_NOT_WRITABLE',      'Папка конфигурации недоступна для записи');

    define('LANG_CRON_1',                   'Для полноценной работы InstantCMS необходимо создать задание для планировщика CRON на сервере.');
    define('LANG_CRON_2',                   'Это позволит системе выполнять периодические служебные задачи в фоновом режиме.');
    define('LANG_CRON_FILE',                'Файл для запуска: <b>%s</b>');
    define('LANG_CRON_INT',                 'Интервал: <b>5 минут</b>');
    define('LANG_CRON_EXAMPLE',             'Обычно, команда которую нужно добавить в планировщик выглядит так:');
    define('LANG_CRON_SUPPORT_1',           'Подробную информацию о настройке CRON можно найти в разделе FAQ на сайте вашего хостинг-провайдера.');
    define('LANG_CRON_SUPPORT_2',           'При затруднении обратитесь в техническую поддержку хостинга, скопировав весь текст выше.');

    define('LANG_FINISH_1',                 'Установка InstantCMS завершена.');
    define('LANG_FINISH_2',                 'Перед тем как продолжить, удалите папку <b>install</b> в корне сайта.');

    define('LANG_FINISH_TO_SITE',           'Перейти на сайт');

    define('LANG_CFG_SITENAME',             'InstantCMS 2.0');
    define('LANG_CFG_HOMETITLE',            'InstantCMS 2.0');
    define('LANG_CFG_DATE_FORMAT',          'd.m.Y');
    define('LANG_CFG_DATE_FORMAT_JS',       'dd.mm.yy');
    define('LANG_CFG_TIME_ZONE',            'Europe/Moscow');
    define('LANG_CFG_METAKEYS',             'ключевые, слова, сайта');
    define('LANG_CFG_METADESC',             'Описание сайта');
