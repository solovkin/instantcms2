<?php
class cmsEventsManager {

    /**
     * Оповещает слушателей о произошедшем событии
     * Входящие данные $data передаются каждому слушателю по очереди,
     * на выходе возвращается измененный слушателями параметр $data
     *
     * @param string $event_name Название события
     * @param mixed $data Параметр события
     * @param mixed $default_return Значение, возвращаемое по-умолчанию если у события нет слушателей
     * @return array Обработанный массив данных
     */
    public static function hook($event_name, $data=false, $default_return=null){

        //получаем все активные контроллеры, привязанные к указанному событию
        $listeners = self::getEventListeners($event_name);

        //если активных контроллеров нет, возвращаем данные без изменений
        if (!$listeners) { return is_null($default_return) ? $data : $default_return; }

        //перебираем контроллеры и вызываем каждый из них, передавая $data
        foreach($listeners as $listener){

            $request = new cmsRequest(array(), cmsRequest::CTX_INTERNAL);

            $controller = cmsCore::getController( $listener, $request );

            $data = $controller->runHook($event_name, array($data));

        }

        return $data;

    }

//============================================================================//
//============================================================================//

    /**
     * Оповещает слушателей о произошедшем событии
     * Входящие данные $data передаются каждому слушателю в изначальном виде,
     * на выходе возвращается массив с ответами от каждого слушателя
     *
     * @param string $event_name Название события
     * @param mixed $data Параметр события
     * @param mixed $default_return Значение, возвращаемое по-умолчанию если у события нет слушателей
     * @return array Обработанный массив данных
     */
    public static function hookAll($event_name, $data=false, $default_return=null){

        //получаем все активные контроллеры, привязанные к указанному событию
        $listeners = self::getEventListeners($event_name);

        //если активных контроллеров нет, возвращаем данные без изменений
        if (!$listeners) { return is_null($default_return) ? false : $default_return; }

        $results = array();

        //перебираем контроллеры и вызываем каждый из них, передавая $data
        foreach($listeners as $listener){

            $request = new cmsRequest(array(), cmsRequest::CTX_INTERNAL);

            $controller = null;

            $controller = cmsCore::getController( $listener, $request );

            $result = $controller->runHook($event_name, array($data));

            if ($result !== false){
                $results[] = $result;
            }

        }

        return $results;

    }

//============================================================================//
//============================================================================//

    /**
     * Возвращает список всех слушателей указанного события
     * @param string $event_name Название события
     * @return array Список слушателей
     */
    public static function getEventListeners($event_name){

        $listeners = array();

        $structure = self::getAllListeners();

        if (isset($structure[ $event_name ])){ $listeners = $structure[ $event_name ]; }

        return $listeners;

    }

//============================================================================//
//============================================================================//

    /**
     * Обновляет кеш списка привязки слушателей к событиям
     * @return boolean
     */
    public static function getAllListeners(){

        $cache = cmsCache::getInstance();
        $cache_key = 'events';

        if (false !== ($structure = $cache->get($cache_key))){
            return $structure;
        }

        $manifests = cmsCore::getControllersManifests();

        if (!$manifests) { return false; }

        $structure = array();

        foreach($manifests as $controller_name => $manifest){

            if (!isset($manifest['hooks'])) { continue; }
            if (!is_array($manifest['hooks'])) { continue; }

            foreach($manifest['hooks'] as $event_name){

                $structure[ $event_name ][] = $controller_name;

            }

        }

        $cache->set($cache_key, $structure, 86400);

        return $structure;

    }

//============================================================================//
//============================================================================//

}