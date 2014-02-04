<?php
class Plugin_PagePostfix extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$conf = Zend_Registry::get('config');
		$controllerName = $request->getControllerName();
		$actionName = $request->getActionName();

		if (strpos($controllerName, $conf->url->postfix))
		{
			$request->setControllerName(str_replace($conf->url->postfix, '', $controllerName));
		}

		if (strpos($actionName, $conf->url->postfix))
		{
			$request->setActionName(str_replace($conf->url->postfix, '', $actionName));
		}
	}

}