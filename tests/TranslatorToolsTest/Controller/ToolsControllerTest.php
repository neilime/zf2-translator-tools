<?php
namespace TranslatorToolsTest\Controller;
use TranslatorTools\Controller\ToolsController;

class ToolsControllerTest extends \PHPUnit_Framework_TestCase{
	/**
	 * @var array
	 */
	private $configuration = array(
	);

	/**
	 * @var \TranslatorTools\Controller\ToolsController
	 */
	protected $controller;

	/**
	 * @var \Zend\Http\Request
	 */
	protected $request;

	/**
	 * @var \Zend\Mvc\Router\RouteMatch
	 */
	protected $routeMatch;

	/**
	 * @var \Zend\Mvc\MvcEvent
	 */
	protected $event;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
    protected function setUp(){
        $oServiceManager = \TranslatorToolsTest\Bootstrap::getServiceManager();

        $this->configuration = \Zend\Stdlib\ArrayUtils::merge($oServiceManager->get('Config'),$this->configuration);
        $bAllowOverride = $oServiceManager->getAllowOverride();
        if(!$bAllowOverride)$oServiceManager->setAllowOverride(true);
        $oServiceManager->setService('Config',$this->configuration)->setAllowOverride($bAllowOverride);

        $this->controller = new \TranslatorTools\Controller\ToolsController();
        $this->request = new \Zend\Http\Request();
        $this->routeMatch = new \Zend\Mvc\Router\RouteMatch(array('controller' => 'tools'));
        $this->event = new \Zend\Mvc\MvcEvent();
        $this->event
        	->setRouter(\Zend\Mvc\Router\Http\TreeRouteStack::factory(isset($this->configuration['router'])?$this->configuration['router']:array()))
        	->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($oServiceManager);
    }
}