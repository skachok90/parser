<?php
class Plugin_Ajax extends Zend_Controller_Plugin_Abstract
{
	public function postDispatch(Zend_Controller_Request_Abstract $request)
	{
		if ($request->isXmlHttpRequest())
		{
			$layout = Zend_Layout::getMvcInstance();
			$view = $layout->getView();
			
			if (!$request->getParam('layout'))
			{
				$layout->disableLayout();
			}

			if ($request->getParam('JSON'))
			{
				$this->_response->clearBody();
				$this->_response->appendBody(Zend_Json::encode($view->getVars()));
			}
		}
	}

}