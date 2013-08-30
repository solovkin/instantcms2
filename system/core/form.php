<?php

class cmsForm {

    public $is_tabbed = false;

    private $structure = array();
    private $disabled_fields = array();

//============================================================================//
//============================================================================//

    /**
     * Заполняет массив полей формы
     * Должен быть переопределен в наследуемом классе
     */
//    public function init(){
//        return array();
//    }

    public function setStructure($structure=array()){
        $this->structure = $structure;
    }

    /**
     * Возвращает массив полей формы
     * @return array
     */
    public function getStructure(){
        return $this->structure;
    }

//============================================================================//
//============================================================================//

    /**
     * Добавляет набор полей в форму.
     * Возращает id набора полей
     * @param string $title Заголовок набора полей
     * @param string $id ID набора полей
     * @return mixed
     */
    public function addFieldset($title='', $id=null){

        if (is_null($id)){
            $id = sizeof($this->structure);
        }

        $this->structure[$id] = array(
            'type' => 'fieldset',
            'title' => $title,
            'childs' => array()
        );

        return $id;

    }

    public function addFieldsetToBeginning($title='', $id=null){

        if (is_null($id)){
            $id = sizeof($this->structure);
        }

        $fieldset = array(
            'type' => 'fieldset',
            'title' => $title,
            'childs' => array()
        );

        $this->structure = array($id => $fieldset) + $this->structure;

        return $id;

    }

    /**
     * Добавляет поле в указанный набор полей формы
     * @param string $fieldset_id ID набора полей
     * @param string $name Название поля
     * @param array $params Параметры поля
     */
    public function addField($fieldset_id, $field){

        $this->structure[ $fieldset_id ]['childs'][] = $field;

    }

    public function addFieldToBeginning($fieldset_id, $field){

        $this->structure[ $fieldset_id ]['childs'] = array($field->name => $field) + $this->structure[ $fieldset_id ]['childs'];

    }

//============================================================================//
//============================================================================//

    /**
     * Изменяет аттрибут набора полей в форме
     * @param string $fieldset_id ID набора полей
     * @param string $attr_name Название аттрибута
     * @param mixed $value Новое значение
     */
    public function setFieldsetAttribute($fieldset_id, $attr_name, $value){

        $this->structure[ $fieldset_id ][ $attr_name ] = $value;

    }

    /**
     * Изменяет аттрибут поля в форме
     * @param string $fieldset_id ID набора полей
     * @param string $field_name Название поля
     * @param string $attr_name Название аттрибута
     * @param mixed $value Новое значение
     */
    public function setFieldAttribute($fieldset_id, $field_name, $attr_name, $value){
        foreach( $this->structure[ $fieldset_id ]['childs'] as $field) {
            if ($field->getName() == $field_name){
                $field->setOption($attr_name, $value);
                break;
            }
        }
    }

//============================================================================//
//============================================================================//

    /**
     * Скрывает набор полей в форме
     * @param string $fieldset_id ID набора полей
     */
    public function hideFieldset($fieldset_id){

        $this->setFieldsetAttribute($fieldset_id, 'is_hidden', true);

    }

    /**
     * Скрывает поле в форме
     * @param string $fieldset_id ID набора полей
     * @param string $field_name Название поля
     */
    public function hideField($fieldset_id, $field_name){

        $this->setFieldAttribute($fieldset_id, $field_name, 'is_hidden', true);

    }

//============================================================================//
//============================================================================//

    /**
     * Удаляет набор полей из формы
     * @param string $fieldset_id ID набора полей
     */
    public function removeFieldset($fieldset_id){
        unset($this->structure[ $fieldset_id ]);
    }

    /**
     * Удаляет поле из формы
     * @param string $fieldset_id ID набора полей
     * @param string $field_name Название поля
     */
    public function removeField($fieldset_id, $field_name){
        foreach( $this->structure[ $fieldset_id ]['childs'] as $field_id => $field) {
            if ($field->getName() == $field_name){
                unset($this->structure[ $fieldset_id ]['childs'][ $field_id ]);
                break;
            }
        }
    }

    /**
     * Отключает поле в форме
     * Поле не удаляется, но перестает участвовать в парсинге и валидации
     *
     * @param string $field_name Название поля
     */
    public function disableField($field_name){
        $this->disabled_fields[] = $field_name;
    }

//============================================================================//
//============================================================================//

    /**
     * Возвращает массив полей формы, заполнив их значениями переданными в запросе $request
     * @param cmsRequest $request
     * @param bool $is_submitted
     * @param array $item
     * @return array
     */
    public function parse($request, $is_submitted=false, $item=false){

        $result = array();

        foreach($this->structure as $fieldset){

            foreach($fieldset['childs'] as $field){

                $name = $field->getName();

                // если поле отключено, пропускаем поле
                if (in_array($name, $this->disabled_fields)){ continue; }

                $is_array = strstr($name, ':');

                $value = $request->get($name, null);

                if (is_null($value) && $field->hasDefaultValue() && !$is_submitted) { $value = $field->getDefaultValue(); }

                $old_value = $item ? (isset($item[$name]) ? $item[$name] : null) : null;

                $value = $field->store($value, $is_submitted, $old_value);

                if ($value === false) { continue; }

                if (!$is_array){
                    $result[$name] = $value;
                }

                if ($is_array){
                    $name_parts = explode(':', $name);
                    $result[$name_parts[0]][$name_parts[1]] = $value;
                }

            }

        }

        return $result;

    }

//============================================================================//
//============================================================================//

    /**
     * Проверяет соответствие массива $data правилам
     * валидации указанным для полей формы
     * @param cmsController $controller
     * @param array $data
     * @param bool $is_check_csrf
     * @return bool Если ошибки не найдены, возвращает false
     */
    public function validate($controller, $data, $is_check_csrf = true){

        $errors = array();

        //
        // Проверяем CSRF-token
        //
        if ($is_check_csrf){
            $csrf_token = $controller->request->get('csrf_token');
            if ( !self::validateCSRFToken( $csrf_token ) ){
                return true;
            }
        }

        //
        // Перебираем поля формы
        //
        foreach($this->structure as $fieldset){
            foreach($fieldset['childs'] as $field){

                $name = $field->getName();

                // если поле отключено, пропускаем поле
                if (in_array($name, $this->disabled_fields)){ continue; }

                // если нет правил, пропускаем поле
                if (!$field->getRules()){ continue; }

                // проверяем является ли поле элементом массива
                $is_array = strstr($name, ':');

                //
                // получаем значение поля из массива данных
                //
                if (!$is_array){
                    $value = isset($data[$name]) ? $data[$name] : '';
                }

                if ($is_array){
                    $name_parts = explode(':', $name);
                    $value = isset($data[$name_parts[0]][$name_parts[1]]) ? $data[$name_parts[0]][$name_parts[1]] : '';
                }

                //
                // перебираем правила для поля
                // и проверяем каждое из них
                //
                foreach($field->getRules() as $rule){

                    if (!$rule) { continue; }

                    // каждое правило это массив
                    // первый элемент - название функции-валидатора
                    $validate_function = "validate_{$rule[0]}";

                    // к остальным элементам добавляем $value, т.к.
                    // в валидаторах $value всегда последний аргумент
                    $rule[] = $value;

                    // убираем название валидатора из массива,
                    // оставляем только параметры (аргументы)
                    unset($rule[0]);

                    // вызываем валидатор и объединяем результат
                    // с предыдущими
                    $result = call_user_func_array(array($controller, $validate_function), $rule);

                    // если получилось false, то дальше не проверяем, т.к.
                    // ошибка уже найдена
                    if (!$result) {
                        $errors[$name] = sprintf(constant('ERR_'.strtoupper($validate_function)), $value);
                        break;
                    }


                }

            }

        }

        if (!sizeof($errors)) { return false; }

        return $errors;

    }

//============================================================================//
//============================================================================//

    /**
     * Создает, сохраняет в сессии и возвращает CSRF-token
     * @return string
     */
    public static function generateCSRFToken($seed=false){

        $hash = md5(session_id()) . rand(0, 9999) . time() . ($seed ? $seed : '');
        $token = md5($hash);

        cmsUser::sessionSet('csrf_token', $token);

        return $token;

    }

    /**
     * Проверяет валидность CSRF-токена
     * @param string $csrf_token
     * @return bool
     */
    public static function validateCSRFToken($csrf_token, $clear=false){
        return (cmsUser::sessionGet('csrf_token', $clear) == $csrf_token);
    }

//============================================================================//
//============================================================================//

    public static function mapFieldsToFieldsets($fields, $callback=null, $values=null){

        $fieldsets = array();

        $current = null;

        $index = 0;

        $fieldsets[ $index ] = array(
            'title' => $current,
            'fields' => array()
        );

        $user = cmsUser::getInstance();

        foreach($fields as $field){

            if (is_callable($callback)){
                if (!$callback( $field, $user )) { continue; }
            }

            if (is_array($values)){
                if (empty($values[ $field['name'] ])){ continue; }
            }

            if ($current != $field['fieldset']){

                $current = $field['fieldset'];
                $index += 1;

                $fieldsets[ $index ] = array(
                    'title' => $current,
                    'fields' => array()
                );

            }

            $fieldsets[ $index ]['fields'][] = $field;

        }

        return $fieldsets;


    }

//============================================================================//
//============================================================================//

    /**
     * Загружает файлы с классами всех типов полей для генерации форм
     */
    public static function loadFormFields(){

        return cmsCore::getFilesList('system/fields', '*.php', false, true);

    }

    /**
     * Возвращает список всех имеющихся типов полей
     * @return array
     */
    public static function getAvailableFormFields($only_public = true){

        $fields_types   = array();
        $fields_files   = cmsCore::getFilesList('system/fields', '*.php', true, true);

        foreach ($fields_files as $name) {

            $class  = 'field' . string_to_camel('_', $name);

            $field = new $class(null, null);

            if ($only_public && !$field->is_public){ continue; }

            $fields_types[$name] = $field->getTitle();

        }

        return $fields_types;

    }

    public static function getForm($form_file, $form_name, $params=false){

        if (!file_exists($form_file)){ return false; }

        cmsForm::loadFormFields();

        include_once $form_file;

        $form_class = 'form' . string_to_camel('_', $form_name);

        $form = new $form_class();

        if ($params){
            $form->setStructure( call_user_func_array(array($form, 'init'), $params) );
        } else {
            $form->setStructure( $form->init() );
        }

        return $form;

    }

//============================================================================//
//============================================================================//

}