<?php
class Resource_Router extends Zend_Application_Resource_ResourceAbstract
{
	public function init()
	{
		$options = $this->getOptions();
		require $options['routes'];

		$controller = Zend_Controller_Front::getInstance();
		$controller->setRouter($router);
		Zend_Registry::set('router', $router);

		return $router;
	}

}