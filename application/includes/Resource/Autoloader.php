<?php
class Resource_Autoloader extends Zend_Application_Resource_ResourceAbstract
{
	public function init()
	{
		$options = $this->getOptions();
		if ($options['enabled'])
		{
			$autoloader = Zend_Loader_Autoloader::getInstance();
			$autoloader->setFallbackAutoloader(true);
		}
	}
}