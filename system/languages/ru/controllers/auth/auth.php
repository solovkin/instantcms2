<?php

    define('LANG_AUTH_CONTROLLER',          'Авторизация и регистрация');

    define('LANG_AUTHORIZATION',            'Авторизация');
    
    define('LANG_AUTH_RESTRICTIONS',            'Ограничения и запреты');
    define('LANG_AUTH_RESTRICTED_EMAILS',       'Запрещенные адреса e-mail');
    define('LANG_AUTH_RESTRICTED_EMAILS_HINT',  'Один адрес на строке, можно использовать символ * для подстановки любого значения');
    define('LANG_AUTH_RESTRICTED_EMAIL',        'Использование e-mail адреса <b>%s</b> запрещено');

    define('LANG_AUTH_RESTRICTED_NAMES',        'Запрещенные никнеймы');
    define('LANG_AUTH_RESTRICTED_NAMES_HINT',   'Один никнейм на строке, можно использовать символ * для подстановки любого значения');
    define('LANG_AUTH_RESTRICTED_NAME',         'Использование никнейма <b>%s</b> запрещено');

    define('LANG_AUTH_RESTRICTED_IPS',          'Запрещенные IP-адреса для регистрации');
    define('LANG_AUTH_RESTRICTED_IPS_HINT',     'Один адрес на строке, можно использовать символ * для подстановки любого значения');
    define('LANG_AUTH_RESTRICTED_IP',           'Регистрация с IP-адреса <b>%s</b> запрещена');

    define('LANG_REG_CFG_REG_CAPTCHA',          'Показывать капчу для защиты от спамовых регистраций');
    define('LANG_REG_CFG_AUTH_CAPTCHA',         'Показывать капчу после неудачной авторизации');
    
    define('LANG_REG_CFG_VERIFY_EMAIL',        'Требовать подтверждения e-mail при регистрации');
    define('LANG_REG_CFG_VERIFY_EMAIL_HINT',   'После регистрации пользователь блокируется до перехода по ссылке из полученного письма');
    define('LANG_REG_CFG_VERIFY_EXPIRATION',   'Удалять неподтвержденные аккаунты через, часов');
    define('LANG_REG_CFG_VERIFY_LOCK_REASON',  'Требуется подтверждение адреса e-mail');

    define('LANG_REG_INCORRECT_EMAIL',       'Некорректный адрес электронной почты');
    define('LANG_REG_EMAIL_EXISTS',          'Указанный адрес электронной почты уже зарегистрирован');
    define('LANG_REG_PASS_NOT_EQUAL',        'Пароли не совпали');
    define('LANG_REG_PASS_EMPTY',            'Необходимо указать пароль');
    define('LANG_REG_SUCCESS',               'Регистрация прошла успешно');
    define('LANG_REG_SUCCESS_NEED_VERIFY',   'На адрес <b>%s</b> отправлено письмо. Перейдите по ссылке из письма чтобы активировать Ваш аккаунт');
    define('LANG_REG_SUCCESS_VERIFIED',      'Адрес e-mail успешно подтвержден. Вы можете авторизоваться на сайте');

    define('LANG_PASS_RESTORE',              'Восстановление пароля');
    define('LANG_EMAIL_NOT_FOUND',           'Указанный E-mail не найден в нашей базе');
    define('LANG_TOKEN_SENDED',              'На указанный адрес E-mail отправлены инструкции по восстановлению пароля');
    define('LANG_RESTORE_NOTICE',            'Пожалуйста укажите адрес E-mail который вы вводили при регистрации.<br/>На указанный адрес будут высланы инструкции по восстановлению пароля.');
