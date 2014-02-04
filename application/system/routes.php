<?php
$router = new Zend_Controller_Router_Rewrite();
$router->addRoutes(array(
	'index'						=> new Zend_Controller_Router_Route_Regex('', array('module' => 'default', 'controller' => 'index', 'action' => 'index'), array(), ''),
	'default:parse'				=> new Zend_Controller_Router_Route_Regex('parse(\.html)?', array('module' => 'default', 'controller' => 'index', 'action' => 'parse'), array(), 'parse.html'),
));
