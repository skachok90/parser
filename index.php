<?php
date_default_timezone_set('Europe/Kiev');
define('APPLICATION_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);

set_include_path(implode(PATH_SEPARATOR, array(
	APPLICATION_PATH . '..' . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR,
	APPLICATION_PATH . 'includes' . DIRECTORY_SEPARATOR . 'Common' . DIRECTORY_SEPARATOR,
	APPLICATION_PATH . 'includes' . DIRECTORY_SEPARATOR,
	APPLICATION_PATH . 'modules' . DIRECTORY_SEPARATOR,
	APPLICATION_PATH . 'models' . DIRECTORY_SEPARATOR,
	APPLICATION_PATH . 'system' . DIRECTORY_SEPARATOR,
		get_include_path(),
)));

require_once 'Zend/Application.php';
require_once 'Zend/Config/Ini.php';
require_once 'Zend/Registry.php';

$conf = new Zend_Config_Ini(APPLICATION_PATH . 'system/config.ini');
Zend_Registry::set('config', $conf->configuration);
$application = new Zend_Application('configuration', $conf->configuration);
$application->bootstrap()->run();
