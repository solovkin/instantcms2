<?php

class cmsTemplate {

    private static $instance;

    private $name;
    private $path;
    private $layout;
    private $output;
    private $options;

	private $head = array();
	private $title;
	private $metadesc;
	private $metakeys;

    private $breadcrumbs = array();
    private $menus = array();

    private $widgets = array();
    private $widgets_group_index = 0;

    private $controller;
    private $controllers_queue;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

// ========================================================================== //
// ========================================================================== //

	function __construct($name=false){

		$config = cmsConfig::getInstance();

        $name = $name ? $name : $config->template;

        $this->setLayout('main');

		$this->head = array();
		$this->title = $config->sitename;

		$this->metakeys = $config->metakeys;
		$this->metadesc = $config->metadesc;

        $this->name = $name;
        $this->path = $config->root_path . 'templates/' . $name;

        $this->options = $this->getOptions();

	}

// ========================================================================== //
// ========================================================================== //

    public function isBody(){
        return !empty($this->output);
    }

	/**
	 * Выводит тело страницы
	 *
	 */
	public function body(){
		echo $this->output;
	}

	/**
	 * Выводит головные теги страницы
	 *
	 */
	public function head(){

        echo "\t". '<meta content="'.htmlspecialchars($this->metakeys).'" name="keywords">' . "\n";
		echo "\t". '<meta content="'.htmlspecialchars($this->metadesc).'" name="description">' ."\n";

		foreach ($this->head as $id=>$tag){	echo "\t". $tag . "\n";	}

	}

	/**
	 * Выводит заголовок текущей страницы
	 * @param string $title
	 */
	public function title(){
		$config = cmsConfig::getInstance();
		if ($this->title){
			echo htmlspecialchars($this->title);
		} else {
			echo htmlspecialchars($config->sitename);
		}
	}

	/**
	 * Выводит название сайта
	 */
	public function sitename(){
		$config = cmsConfig::getInstance();
		echo htmlspecialchars($config->sitename);
	}

    /**
     * Выводит глобальный тулбар
     */
    public function toolbar(){
        if (!$this->isToolbar()){ return; }
        $this->menu('toolbar', false);
    }

    /**
     * Выводит виджеты на указанной позиции
     * @param string $position Название позиции
     * @param boolean $is_titles Выводить заголовки
     * @param string $wrapper Название шаблона обертки
     * @return boolean
     */
	public function widgets($position, $is_titles=true, $wrapper=false){

        if (!$this->hasWidgetsOn($position)){ return false; }

        foreach($this->widgets[$position] as $group){

            if (sizeof($group)==1){

                $widget = $group[0];
                if ($wrapper){ $widget['wrapper'] = $wrapper; }
                $tpl_file = $this->getTemplateFileName('widgets/' . $widget['wrapper']);
                include($tpl_file);

            } else {

                $widgets = $group;
                $tpl_file = $this->getTemplateFileName('widgets/wrapper_tabbed');
                include($tpl_file);

            }

        }

	}

    public function hasWidgetsOn($position){

        if (func_num_args() > 1){
            $positions = func_get_args();
        } else {
            $positions = array($position);
        }

        $has = false;

        foreach($positions as $pos){
            $has = $has || !empty($this->widgets[$pos]);
        }

        return $has;

    }

    /**
     * Выводит меню
     */
    public function menu($menu_name, $detect_active_id=true, $css_class='menu', $max_items=0){

        $core = cmsCore::getInstance();
        $config = cmsConfig::getInstance();

        if (!isset($this->menus[$menu_name])) {
            $menu_model = cmsCore::getModel('menu');
            $menu = $menu_model->getMenu($menu_name, 'name');
            if (!$menu){ return; }
            $items = $menu_model->getMenuItemsTree($menu['id']);
            if (!$items){ return; }
            $this->addMenuItems($menu_name, $items);
        }

        $active_id = false;

        if ($detect_active_id){

            $current_url = trim($core->uri, '/');

            //перебираем меню в поисках текущего пункта
            foreach($this->menus[$menu_name] as $id=>$item){

                if (!isset($item['url']) && !isset($item['controller'])) { continue; }

                if (!isset($item['url'])) {
                    if (!isset($item['action'])) { $item['action'] = ''; }
                    if (!isset($item['params'])) { $item['params'] = array(); }
                    $item['url'] = href_to($item['controller'], $item['action'], $item['params']);
                    $this->menus[$menu_name][$id]['url'] = $item['url'];
                    $menu[$id] = $item;
                }

                $url = isset($item['url_mask']) ? $item['url_mask'] : $item['url'];
                $url = mb_substr($url, mb_strlen($config->root));
                $url = trim($url, '/');

                if (!$url) { continue; }

                //полное совпадение ссылки и адреса?
                if ($current_url == $url){
                    $active_id = $id;
                    $is_strict = true;
                } else {

                    //частичное совпадение ссылки и адреса (по началу строки)?
                    $url_first_part = mb_substr($current_url, 0, mb_strlen($url));
                    if ($url_first_part == $url){
                        $active_id = $id;
                        $is_strict = false;
                    }

                }

            }

        }

        $this->renderMenu($this->menus[$menu_name], $active_id, $css_class, $max_items);

    }

    /**
     * Выводит глубиномер
     * @return <type>
     */
    public function breadcrumbs($options=array()){

        $config = cmsConfig::getInstance();

        $default_options = array(
            'home_url' => $config->host,
            'strip_last' => true
        );

        $options = array_merge($default_options, $options);

        if ($this->breadcrumbs){
            if ($options['strip_last']){
                unset($this->breadcrumbs[sizeof($this->breadcrumbs)-1]);
            } else {
                $this->breadcrumbs[sizeof($this->breadcrumbs)-1]['is_last'] = true;
            }
        }

        $this->renderAsset('ui/breadcrumbs', array(
            'breadcrumbs' => $this->breadcrumbs,
            'options' => $options
        ));

    }

    public function href_to($action, $params=false){

        if (!isset($this->controller->root_url)){
            return href_to($this->controller->name, $action, $params);
        } else {
            return href_to($this->controller->root_url, $action, $params);
        }

    }

//============================================================================//
//============================================================================//

    /**
     * Добавляет переданный код к выводу
     * @param str $html
     */
    public function addOutput($html){
        $this->output .= $html;
    }

    /**
     * Принудительно печатает вывод
     */
    public function printOutput() {
        echo $this->output;
    }

// ========================================================================== //
// ========================================================================== //

	/**
	 * Устанавливает заголовок страницы
     * Если передано несколько аргументов, склеивает их в одну строку
     * через разделитель
     *
	 * @param string $pagetitle Заголовок
	 */
	public function setPageTitle($pagetitle){
		$config = cmsConfig::getInstance();
        if (func_num_args() > 1){ $pagetitle = implode(' - ', func_get_args()); }
        $this->title = $pagetitle;
        $this->title .= ' - '.$config->sitename;
	}

	/**
	 * Устанавливает ключевые слова и описание страницы
	 * @param str $keywords
	 * @param str $description
	 */
	public function setMeta($keywords, $description){
		$this->metakeys = $keywords;
		$this->metadesc = $description;
	}

	/**
	 * Устанавливает ключевые слова страницы
	 * @param str $keywords
	 */
    public function setPageKeywords($keywords){
        $this->metakeys = $keywords;
    }

	/**
	 * Устанавливает описание страницы
	 * @param str $description
	 */
    public function setPageDescription($description){
        $this->metadesc = $description;
    }

// ========================================================================== //
// ========================================================================== //

    /**
     * Добавляет кнопку на глобальный тулбар
     * @param array $button
     */
    public function addToolButton($button){

        $item = array(
            'title' => $button['title'],
            'url' => isset($button['href']) ? $button['href'] : '',
            'level' => 1,
            'options' => array(
                'class' => isset($button['class']) ? $button['class'] : null,
                'target' => isset($button['target']) ? $button['target'] : '',
                'onclick' => isset($button['onclick']) ? $button['onclick'] : null,
                'confirm' => isset($button['confirm']) ? $button['confirm'] : null,
            )
        );

        $this->addMenuItem('toolbar', $item);

    }

    /**
     * Проверяет наличие кнопок на тулбаре
     * @return bool
     */
    public function isToolbar(){
        if (!isset($this->menus['toolbar'])){ return false; }
        return (bool)sizeof($this->menus['toolbar']);
    }

// ========================================================================== //
// ========================================================================== //

    public function addMenuItem($menu_name, $item){

        if (!isset($this->menus[$menu_name])){
            $this->menus[$menu_name] = array();
        }

        array_push($this->menus[$menu_name], $item);

    }

    public function addMenuItems($menu_name, $items){

        if (!isset($this->menus[$menu_name])){
            $this->menus[$menu_name] = array();
        }

        foreach($items as $item){
            if (!isset($item['level'])) { $item['level'] = 1; }
            array_push($this->menus[$menu_name], $item);
        }

    }

    public function setMenuItems($menu_name, $items){

        if (!$items) { return; }

        $this->menus[$menu_name] = $items;

    }

// ========================================================================== //
// ========================================================================== //

    public function addBreadcrumb($title, $href=''){

        if (!$href) { $href = $_SERVER['REQUEST_URI']; }

        $this->breadcrumbs[] = array('title'=>$title, 'href'=>$href);

    }

    /**
     * Проверяет наличие пунктов в глубиномере
     * @return bool
     */
    public function isBreadcrumbs(){
        return (bool)$this->breadcrumbs;
    }

// ========================================================================== //
// ========================================================================== //

	/**
	 * Добавляет тег в головной раздел страницы
	 * @param string $tag
	 */
	public function addHead($tag){
		$this->head[] = $tag;
	}

	/**
	 * Добавляет CSS файл в головной раздел страницы
	 * @param string $file
	 */
	public function addCSS($file){
		$config = cmsConfig::getInstance();
        $file = strstr($file, 'http://') ? $file : $config->root . $file;
        $hash = md5($file);
		$this->head[$hash] = '<link rel="stylesheet" type="text/css" href="'.$file.'">';
	}

	/**
	 * Добавляет JS файл в головной раздел страницы
	 * @param string $file
	 */
	public function addJS($file, $comment=''){

        $hash = md5($file);
        if (isset($this->head[$hash])) { return false; }

		$config = cmsConfig::getInstance();
        $file = strstr($file, 'http://') ? $file : $config->root . $file;
        $comment = $comment ? "<!-- {$comment} !-->" : '';
        $tag = '<script type="text/javascript" src="'.$file.'">'.$comment.'</script>';
        $this->head[$hash] = $tag;

        return true;

	}

	public function insertJS($file, $comment=''){

		$config = cmsConfig::getInstance();
        $file = strstr($file, 'http://') ? $file : $config->root . $file;
        $comment = $comment ? "<!-- {$comment} !-->" : '';
        $tag = '<script type="text/javascript" src="'.$file.'">'.$comment.'</script>';

        echo $tag;

	}

    public function insertCSS($file){

		$config = cmsConfig::getInstance();
        $file = strstr($file, 'http://') ? $file : $config->root . $file;
		$tag = '<link rel="stylesheet" type="text/css" href="'.$file.'">';

        echo $tag;

    }

    public function getJS($file){

        ob_start();
        $this->insertJS($file);
        return ob_get_clean();

    }

    public function getCSS($file){

        ob_start();
        $this->insertCSS($file);
        return ob_get_clean();

    }

    public function getLangJS($phrase){

        $value = htmlspecialchars(constant($phrase));

        return "var {$phrase} = '{$value}';";

    }

// ========================================================================== //
// ========================================================================== //

    /**
     * Устанавливает шаблон скелета
     * @param string $layout
     */
    public function setLayout($layout){
        $this->layout = $layout;
    }

    /**
     * Возвращает название шаблона скелета
     * @param string $layout
     */
    public function getLayout(){
        return $this->layout;
    }

    /**
     * Возвращает HTML-разметку схемы позиций виджетов
     * @return string
     */
    public function getSchemeHTML(){

        $config = cmsConfig::getInstance();

        $scheme_file = $config->root_path . 'templates/'.$this->name.'/scheme.html';

        if (!file_exists($scheme_file)) { return false; }

        $scheme_html = file_get_contents($scheme_file);

        return $scheme_html;

    }

// ========================================================================== //
// ========================================================================== //

    /**
     * Возвращает название глобального шаблона
     * @return string
     */
    public function getName(){
        return $this->name;
    }

// ========================================================================== //
// ========================================================================== //

    /**
     * Сохраняет ссылку на текущий контроллер
     * @param string $controller_obj
     */
    public function setContext($controller_obj){
        if ($this->controller) { $this->controllers_queue[] = $this->controller; }
        $this->controller = $controller_obj;
    }

    /**
     * Восстанавливает ссылку на предыдущий контроллер
     */
    public function restoreContext(){

        if (!sizeof($this->controllers_queue)) { return false; }

        $this->controller = array_pop($this->controllers_queue);

        return true;

    }

// ========================================================================== //
// ========================================================================== //

    /**
     * Возвращает путь к tpl-файлу, определяя его наличие в собственном шаблоне
     * @param str $filename
     * @return string
     */
    public function getTemplateFileName($filename, $is_check=false){

        $config = cmsConfig::getInstance();

        $default    = $config->root_path . 'templates/default/'.$filename.'.tpl.php';
        $tpl_file   = $config->root_path . 'templates/'.$this->name.'/'.$filename.'.tpl.php';

        if (!file_exists($tpl_file)) { $tpl_file = $default; }

        if (!file_exists($tpl_file)){
            if (!$is_check){
                cmsCore::error(ERR_TEMPLATE_NOT_FOUND . ': ' . $tpl_file);
            } else {
                return false;
            }
        }

        return $tpl_file;

    }

    /**
     * Возвращает путь к CSS-файлу, определяя его наличие в собственном шаблоне
     * @param str $filename
     * @return string
     */
    public function getStylesFileName(){

        $config = cmsConfig::getInstance();

        $default    = 'templates/default/controllers/'.$this->controller->name.'/styles.css';
        $tpl_file   = 'templates/'.$this->name.'/controllers/'.$this->controller->name.'/styles.css';

        if (!file_exists($config->root_path . $tpl_file)) { $tpl_file = $default; }

        if (!file_exists($config->root_path . $tpl_file)){ return false; }

        return $tpl_file;

    }

    /**
     * Возвращает путь к CSS-файлу, определяя его наличие в собственном шаблоне
     * @param str $filename
     * @return string
     */
    public function getJavascriptFileName($filename){

        $config = cmsConfig::getInstance();

        $default    = 'templates/default/js/'.$filename.'.js';
        $js_file   = 'templates/'.$this->name.'/js/'.$filename.'.js';

        if (!file_exists($config->root_path . $js_file)) { $js_file = $default; }

        if (!file_exists($config->root_path . $js_file)){ return false; }

        return $js_file;

    }

//============================================================================//
//============================================================================//

    public function renderText($text){

        echo $this->addOutput($text);

    }

    public function renderJSON($data){

        echo json_encode($data);

        if ($this->controller->request->isAjax()) { $this->controller->halt(); }

    }

    public function renderInternal($controller, $tpl_file, $data=array()){

        $this->setContext($controller);

        $result = $this->render($tpl_file, $data, new cmsRequest(array(), cmsRequest::CTX_INTERNAL));

        $this->restoreContext($result);

        return $result;

    }

    /**
     * Выводит массив $data в шаблон $tpl_file (в папке шаблонов этого компонента)
     * @param string $tpl_file
     * @param array $data
     */
    public function render($tpl_file, $data=array(), $request=false){

        $css_file = $this->getStylesFileName();

        if ($css_file){ $this->addCSS($css_file); }

        $tpl_file = $this->getTemplateFileName('controllers/'.$this->controller->name.'/'.$tpl_file);

        return $this->processRender($tpl_file, $data, $request);

    }

    public function processRender($tpl_file, $data=array(), $request=false){

        if (!$request) { $request = $this->controller->request; }

        ob_start();

        extract($data); include($tpl_file);

        $html = ob_get_clean();

        if ($request->isAjax()) {
            echo $html;
            $this->controller->halt();
        }

        if ($request->isStandard()){
            $this->addOutput( $html );
            return $html;
        }

        if ($request->isInternal()){
            return $html;
        }

    }

    /**
     * Выводит массив $data в шаблон $tpl_file (в папке шаблонов этого компонента)
     * @param string $tpl_file
     * @param array $data
     */
    public function renderChild($tpl_file, $data=array()){

        $tpl_file = $this->getTemplateFileName('controllers/'.$this->controller->name.'/'.$tpl_file);

        extract($data); include($tpl_file);

    }

    /**
     * Выводит массив $data в шаблон $tpl_file (в папке шаблонов этого компонента)
     * @param string $tpl_file
     * @param array $data
     */
    public function renderForm($form, $data, $attributes=array(), $errors=false){

        $tpl_file = $this->getTemplateFileName('assets/ui/form');

        include($tpl_file);

    }

    /**
     * Выводит массив $data в шаблон $tpl_file (в папке шаблонов этого компонента)
     * @param string $source_url
     * @param array $grid
     */
    public function renderGrid($source_url, $grid){

        $this->addJS( $this->getJavascriptFileName('datagrid') );

        if ($grid['options']['is_pagination']){
            $this->addJS( $this->getJavascriptFileName('datagrid-pagination') );
        }

        if ($grid['options']['is_draggable']){
            $this->addJS( $this->getJavascriptFileName('datagrid-drag') );
        }

        $tpl_file = $this->getTemplateFileName('assets/ui/grid-data');

        extract($grid);

        include($tpl_file);

    }

    public function renderGridRowsJSON($grid, $dataset, $total=1, $pages_count=1){

        $rows = array();
        $row_index = 0;

        //
        // проходим по всем строкам из набора данных
        //
        if ($total && $dataset){
            foreach($dataset as $row){

                $cell_index = 0;

                // вычисляем содержимое для каждой колонки таблицы
                foreach($grid['columns'] as $field => $column){

                    $value = $row[$field];

                    if (!$value) { $value = ''; }

                    if (isset($column['flag']) && $column['flag']){
                        $class_base = $column['flag']===true ? 'flag' : $column['flag'];
                        $value = '<div class="flag_trigger '.($value ? "{$class_base}_on" : "{$class_base}_off").'"></div>';
                    }

                    // если из значения нужно сделать ссылку, то парсим шаблон
                    // адреса, заменяя значения полей
                    if (isset($column['href'])){
                        foreach($row as $cell_id=>$cell_value){
                            if (is_array($cell_value) || is_object($cell_value)) { continue; }
                            $column['href'] = str_replace('{'.$cell_id.'}', $cell_value, $column['href']);
                        }
                        $value = '<a href="'.$column['href'].'">'.$value.'</a>';
                    }

                    if (isset($column['handler'])){
                        $value = $column['handler']($value, $row);
                    }

                    $rows[$row_index][] = $value;

                    $cell_index++;

                }

                // если есть колонка действий, то формируем набор ссылок
                // для текущей строки
                if ($grid['actions']){

                    $actions_html = '<div class="actions">';
                    $confirm_attr = '';

                    foreach($grid['actions'] as $action){

                        if (isset($action['handler'])){
                            $is_active = $action['handler']($row);
                        } else {
                            $is_active = true;
                        }

                        if ($is_active){
                            foreach($row as $cell_id=>$cell_value){

                                if (is_array($cell_value) || is_object($cell_value)) { continue; }

                                // парсим шаблон адреса, заменяя значения полей
                                if (isset($action['href'])){
                                    $action['href'] = str_replace('{'.$cell_id.'}', $cell_value, $action['href']);
                                }

                                // парсим шаблон запроса подтверждения, заменяя значения полей
                                if (isset($action['confirm'])){
                                    $action['confirm'] = str_replace('{'.$cell_id.'}', $cell_value, $action['confirm']);
                                    $confirm_attr = 'onclick="if(!confirm(\''.htmlspecialchars($action['confirm']).'\')){ return false; }"';
                                }

                            }

                            $actions_html .= '<a class="'.$action['class'].'" href="'.$action['href'].'" title="'.$action['title'].'" '.$confirm_attr.'></a>';
                        }

                    }

                    $actions_html .= '</div>';

                    $rows[$row_index][] = $actions_html;

                    $cell_index++;

                }

                $row_index++;
            }
        }

        $result = array(
            'rows' => $rows,
            'pages_count' => $pages_count,
            'total' => $total
        );

        echo json_encode($result);

    }

    /**
     * Выводит таблицу прав доступа по группам пользователей
     * @param array $rules Массив правил
     * @param array $groups Массив групп пользователей
     * @param array $values Массив значений
     * @param string $submit_url URL для сохранения формы
     */
    public function renderPermissionsGrid($rules, $groups, $values, $submit_url){

        $this->addJS( $this->getJavascriptFileName('datagrid') );

        $tpl_file = $this->getTemplateFileName('assets/ui/grid-perms');

        include($tpl_file);

    }

    /**
     * Выводит меню
     * @param array $items
     * @param int $active_id
     */
    public function renderMenu($menu, $active_id=false, $css_class='menu', $max_items=0){

        $tpl_file = $this->getTemplateFileName('assets/ui/menu');

        include($tpl_file);

    }

    public function renderAsset($tpl_file, $data=array()){

        $tpl_file = $this->getTemplateFileName('assets/' . $tpl_file);

        extract($data); include($tpl_file);

    }

    public function renderFormField($field_type, $data=array()){

        $tpl_file = $this->getTemplateFileName('assets/fields/'.$field_type);

        ob_start();

        extract($data); include($tpl_file);

        $html = ob_get_clean();

        return $html;

    }

//============================================================================//
//============================================================================//

    /**
     * Рендерит шаблон списка записей контента
     */
    public function renderContentList($ctype_name, $data=array(), $request=false){

        $tpl_file = $this->getTemplateFileName('content/'.$ctype_name.'_list', true);

        if (!$tpl_file){ $tpl_file = $this->getTemplateFileName('content/default_list', true); }

        if (!$request) { $request = $this->controller->request; }

        return $this->processRender($tpl_file, $data, $request);

    }

    /**
     * Рендерит шаблон просмотра записи контента
     */
    public function renderContentItem($ctype_name, $data=array(), $request=false){

        $tpl_file = $this->getTemplateFileName('content/'.$ctype_name.'_item', true);

        if (!$tpl_file){ $tpl_file = $this->getTemplateFileName('content/default_item', true); }

        if (!$request) { $request = $this->controller->request; }

        return $this->processRender($tpl_file, $data, $request);

    }

//============================================================================//
//============================================================================//

    /**
     * Выводит окончательный вид страницы в браузер
     */
    public function renderPage(){

        $config = cmsConfig::getInstance();

        $layout = $this->getLayout();

        $template_file = $this->path . '/' . $layout . '.tpl.php';

        if(file_exists($template_file)){

            if (!$config->min_html){
                include($template_file);
            }

            if ($config->min_html){
                ob_start();
                include($template_file);
                echo html_minify(ob_get_clean());
            }

        } else {
            cmsCore::error(ERR_TEMPLATE_NOT_FOUND. ': '. $this->name.':'.$layout);
        }

    }

//============================================================================//
//============================================================================//

    public function renderWidget($widget, $data=array()){

        $tpl_path = cmsCore::getWidgetPath($widget->name, $widget->controller);

        $tpl_file = $this->getTemplateFileName($tpl_path . '/' . $widget->getTemplate());

        extract($data);

        ob_start(); include($tpl_file);

        $html = ob_get_clean();

        if (!$html){ return true; }

        if (empty($widget->is_tab_prev)){
            $this->widgets_group_index++;
        }

        $this->widgets[$widget->position][$this->widgets_group_index][] = array(
            'id' => $widget->id,
            'title' => $widget->is_title ? $widget->title : false,
            'links' => isset($widget->links) ? $widget->links : false,
            'wrapper' => $widget->getWrapper(),
            'class' => isset($widget->css_class) ? $widget->css_class : false,
            'body' => $html
        );

    }

//============================================================================//
//============================================================================//

    public function hasOptions(){
        return file_exists($this->path . '/options.form.php');
    }

    public function getOptionsForm(){

        if (!$this->hasOptions()){ return false; }

        cmsCore::loadTemplateLanguage($this->name);

        $form_file = $this->path . '/options.form.php';

        $form_name = 'template_options';

        $form = cmsForm::getForm($form_file, $form_name);

        if (!$form) { $form = new cmsForm(); }

        return $form;

    }

    public function getOptions(){

        $options = $this->loadOptions();

        $form = $this->getOptionsForm();

        $options = $form->parse(new cmsRequest($options));

        return $options;

    }

    public function loadOptions(){

        if (!$this->hasOptions()){ return false; }

        $config = cmsConfig::getInstance();

        $options_file = $config->root_path . "system/config/theme_{$this->name}.yml";

        if (!file_exists($options_file)){ return array(); }

        $options_yaml = @file_get_contents($options_file);

        $options = cmsModel::yamlToArray($options_yaml);

        return $options;

    }

    public function saveOptions($options){

        $config = cmsConfig::getInstance();

        $options_file = $config->root_path . "system/config/theme_{$this->name}.yml";

        $options_yaml = cmsModel::arrayToYaml($options);

        return @file_put_contents($options_file, $options_yaml);

    }


//============================================================================//
//============================================================================//

    public function hasProfileThemesSupport(){
        return file_exists($this->path . '/profiles/styler.php');
    }

    public function hasProfileThemesOptions(){
        return file_exists($this->path . '/profiles/options.form.php');
    }

    public function getProfileOptionsForm(){

        if (!$this->hasProfileThemesOptions()){ return false; }

        $form_file = $this->path . '/profiles/options.form.php';

        $form_name = 'template_profile_options';

        $form = cmsForm::getForm($form_file, $form_name);

        if (!$form) { $form = new cmsForm(); }

        return $form;

    }

    public function applyProfileStyle($profile){

        if (!$this->hasProfileThemesSupport()){ return false; }

        $config = cmsConfig::getInstance();

        $theme = $profile['theme'];

        cmsCore::loadTemplateLanguage($this->name);

        if ($this->hasProfileThemesOptions()){

            $form = $this->getProfileOptionsForm();
            $theme = $form->parse(new cmsRequest($profile['theme']), true);

        }

        ob_start();

        extract($theme);

        include $this->path . '/profiles/styler.php';

        $style = ob_get_clean();

        $this->addHead($style);

        return true;

    }

//============================================================================//
//============================================================================//

}
