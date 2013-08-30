<?php

class actionAdminInstallFinish extends cmsAction {

    public function run(){

        $config = cmsConfig::getInstance();

        $path = $config->upload_path . $this->installer_upload_path;
        $path_relative = $config->upload_root . $this->installer_upload_path;

        $installer_path = $path . '/' . 'install.php';

        @chmod($installer_path, 0755);

        $this->runPackageInstaller($installer_path);

        $is_cleared = files_clear_directory($path);

        return cmsTemplate::getInstance()->render('install_finish', array(
            'is_cleared' => $is_cleared,
            'path_relative' => $path_relative,
        ));

    }

    public function runPackageInstaller($installer_path){

        if (!file_exists($installer_path)) { return false; }

        include_once $installer_path;

        if (!function_exists('install_package')){ return false; }

        return call_user_func('install_package');

    }

}
