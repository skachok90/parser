<?php
abstract class Controller_Abstract extends Zend_Controller_Action
{
	protected static $config 			= null;
	
	protected $_params					= array();
	protected $_userParams				= array();
	
	public function init()
	{
		if(!self::$config)
		{
			self::$config = Zend_Registry::get('config');
		}
		
		$this->_params = $this->_request->getParams();
		$this->_userParams = $this->_request->getUserParams();
	}
}
