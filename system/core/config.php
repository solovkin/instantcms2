<?php

class cmsConfig {

    private static $instance;

    private $data = array();
    private $dynamic = array();

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public static function get($key){
        return self::getInstance()->$key;
    }

    public static function getControllersMapping(){

        $map_file = 'system/config/remap.php';
        $map_function = 'remap_controllers';

        if (!cmsCore::includeFile($map_file)) { return false; }

        if (!function_exists($map_function)){ return false; }

        $mapping = call_user_func($map_function);

        if (!is_array($mapping)){ return false; }

        return $mapping;

    }

//============================================================================//
//============================================================================//

	public function __construct($cfg_file='config.php'){

        $cfg_file = PATH . '/system/config/' . $cfg_file;

        $configuration = require_once $cfg_file;

        foreach ($configuration as $option=>$value) { $this->data[$option] = $value; }

        if (isset($_SESSION['user']['time_zone'])){
            $this->data['time_zone'] = $_SESSION['user']['time_zone'];
        }

        $this->set('root_path', ROOT . $this->root);
        $this->set('system_path', $this->root_path . 'system/');
        $this->set('upload_path', ROOT . $this->upload_root);
        $this->set('cache_path', ROOT . $this->cache_root);

	}

//============================================================================//
//============================================================================//

    public function set($key, $value){
        $this->data[$key] = $value;
        $this->dynamic[] = $key;
    }

    public function getAll(){
        return $this->data;
    }

    public function __get($name) {
        return $this->data[$name];
    }

//============================================================================//
//============================================================================//

    public function updateTimezone(){

        if (isset($_SESSION['user']['time_zone'])){
            $this->data['time_zone'] = $_SESSION['user']['time_zone'];
        }

        date_default_timezone_set( $this->data['time_zone'] );

        cmsDatabase::getInstance()->setTimezone();

    }

//============================================================================//
//============================================================================//

    public function save($values, $cfg_file='config.php'){

        $dump = "<?php\n" .
                "return array(\n\n";

        foreach($values as $key=>$value){

            if (in_array($key, $this->dynamic)){ continue; }

            if (!is_numeric($value)) { $value = "'{$value}'"; }

            $tabs = 7 - ceil((mb_strlen($key)+3)/4);

            $dump .= "\t'{$key}'";
            $dump .= str_repeat("\t", $tabs);
            $dump .= "=> $value,\n";

        }

        $dump .= "\n);\n";

        $file = self::get('root_path').'system/config/' . $cfg_file;

        return @file_put_contents($file, $dump);

    }

}
