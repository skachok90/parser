<?php
class Resource_Db extends Zend_Application_Resource_ResourceAbstract
{
	public function init()
	{
		$options = $this->getOptions();
		$db = Zend_Db::factory($options['adapter'], $options['params']);
		Zend_Db_Table_Abstract::setDefaultAdapter($db);
		Zend_Registry::set('db', $db);

		return $db;
	}

}