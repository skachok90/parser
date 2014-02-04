<?php
class Resource_Session extends Zend_Application_Resource_ResourceAbstract
{
	public function init()
	{
		$request = new Zend_Controller_Request_Http();
		$params = $request->getParams();

		if ($params['PHPSESSID'])
		{
			Zend_Session::setId($params['PHPSESSID']);
		}

		$options = $this->getOptions();
		$session = new Zend_Session_Namespace($options['name']);
		Zend_Registry::set('session', $session);

		return $session;
	}

}
