<?php
namespace TranslatorToolsTest\Controller;
use TranslatorTools\Controller\ToolsController;

class ToolsControllerTest extends \PHPUnit_Framework_TestCase{
	/**
	 * @var array
	 */
	private $configuration;

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

        $this->configuration = \Zend\Stdlib\ArrayUtils::merge($oServiceManager->get('Config'),include __DIR__.'/../configuration.php');
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

    public function testService(){
    	$oTranslatorToolsService = $this->controller->getServiceLocator()->get('TranslatorToolsService');

    	//Test service instance
    	$this->assertInstanceOf('TranslatorTools\Service\TranslatorToolsService',$oTranslatorToolsService);
    }

    public function testListLocales(){
    	$this->routeMatch->setParam('action', 'listLocales');
    	$this->controller->dispatch($this->request);
    	$this->assertEquals(200, $this->controller->getResponse()->getStatusCode());
    }
}