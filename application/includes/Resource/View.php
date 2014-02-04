<?php
class Resource_View extends Zend_Application_Resource_ResourceAbstract
{
	public function init()
	{
		$options = $this->getOptions();
		$conf = Zend_Registry::get('config');
		$frontController = Zend_Controller_Front::getInstance();
		$layout = Zend_Layout::startMvc();
		
		$view = $layout->getView();
		
		$view->doctype(Zend_View_Helper_Doctype::XHTML1_TRANSITIONAL);
		
		$view
		->headTitle()
		->append($options['title']);
		
		$view
		->headMeta()
		->appendHttpEquiv('Content-Type', 'text/html; charset=' . $options['encoding'])
		->appendName('description', $options['description'])
		->appendName('keywords', $options['keywords']);
	
		/*$view
		->headLink()
		->appendStylesheet($conf->url->css . 'main.css');
		
		$view
		->headScript()
		->appendFile($conf->url->js . 'main.js');*/
		
		$view
		->assign(array(
			'urlBase' => $conf->url->base,
			'urlImg' => $conf->url->img,
			'urlJs' => $conf->url->js,
			'urlCss' => $conf->url->css,
		))
		->addHelperPath(array(
			$frontController->getModuleDirectory() . DIRECTORY_SEPARATOR . end(reset($view->getHelperPaths())),
		));
		
		$layout
		->setViewSuffix('tpl')
		->setView($view);
	
		$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
		$viewRenderer
		->setView($view)
		->setViewSuffix('tpl');
		
		Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
		
		Zend_Paginator::setDefaultScrollingStyle('Sliding');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('partials/pagination.tpl');
	
		return $view;
	}
}