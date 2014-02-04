<?php
class Plugin_Layout extends Zend_Controller_Plugin_Abstract
{
	const LAYOUT			= 'layout';

	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$moduleName = $request->getModuleName();	
		$controllerName = $request->getControllerName();	
		$actionName = $request->getActionName();
		
		$layout = self::LAYOUT;
		
		Zend_Layout::getMvcInstance()->setLayout($layout);
	}
}
