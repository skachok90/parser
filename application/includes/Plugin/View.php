<?php
class Plugin_View extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$frontController = Zend_Controller_Front::getInstance();	
		$layout = Zend_Layout::getMvcInstance();
		$view = $layout->getView();
		$currentModule = $request->getModuleName();

		$view->assign(array(
			'module' => $currentModule,
			'controller' => $request->getControllerName(),
			'action' => $request->getActionName(),
		))
		->addBasePath($frontController->getModuleDirectory($frontController->getDefaultModule()) . DIRECTORY_SEPARATOR . 'views')
		;
		
		$layout->setLayoutPath(array(
			$frontController->getModuleDirectory($frontController->getDefaultModule()) . DIRECTORY_SEPARATOR . reset($view->getScriptPaths()),
			$frontController->getModuleDirectory($currentModule) . DIRECTORY_SEPARATOR . reset($view->getScriptPaths()),
		));
		
	}

}
