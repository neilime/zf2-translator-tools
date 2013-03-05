<?php
namespace TranslatorToolsTest;
class ServiceTest extends \PHPUnit_Framework_TestCase{
	/**
	 * @var array
	 */
	private $configuration = array(
		'translator' => array(

		)
	);

	/**
	 * @var \TranslatorTools\Service\TranslatorToolsService
	 */
	private $service;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
    protected function setUp(){
        $oServiceManager = \TranslatorToolsTest\Bootstrap::getServiceManager();

        $this->configuration = \Zend\Stdlib\ArrayUtils::merge($oServiceManager->get('Config'),$this->configuration);
        $bAllowOverride = $oServiceManager->getAllowOverride();
        if(!$bAllowOverride)$oServiceManager->setAllowOverride(true);
        $oServiceManager->setService('Config',$this->configuration)->setAllowOverride($bAllowOverride);

        //Define service
        $this->service = $oServiceManager->get('TranslatorToolsService');
    }

    public function testService(){
    	//Test service instance
    	$this->assertInstanceOf('TranslatorTools\Service\TranslatorToolsService',$this->service);
    }

    public function testGetLocales(){
		$this->assertEquals($this->service->getLocales(),array('fr_FR','en_US'));
    }

    public function testGetTextDomains(){
    	$this->assertEquals($this->service->getTextDomains(),array('default','other-domain'));
    }

    public function testGetAvailableMessages(){
    	$this->assertEquals($this->service->getAvailableMessages(),array());
    }
}