<?php

class View_Helper_Abstract extends Zend_View_Helper_Abstract
{
	protected static $config 			= null;
	
	public function __construct()
	{
		if(!self::$config)
		{
			self::$config = Zend_Registry::get('config');
		}

	}
	
	// Return action helper
	protected function actionHelper($name)
	{
		return Zend_Controller_Action_HelperBroker::getStaticHelper($name);
	}
}