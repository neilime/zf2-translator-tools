<?php
namespace TranslatorToolsTest;
class ServiceTest extends \PHPUnit_Framework_TestCase{
	/**
	 * @var \TranslatorTools\Service\TranslatorToolsService
	 */
	private $service;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
    protected function setUp(){
        $oServiceManager = \TranslatorToolsTest\Bootstrap::getServiceManager();

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

    public function testGetTranslationFileInfos(){
    	$this->assertEquals($this->service->getTranslationFileInfos('default','fr_FR'),array(
			'pathname' => getcwd().'/TranslatorToolsTest/_files/default/fr_FR.php',
			'type' => 'array'
		));
    	$this->assertEquals($this->service->getTranslationFileInfos('default','en_US'),array(
			'pathname' => getcwd().'/TranslatorToolsTest/_files/default/en_US.php',
			'type' => 'array'
		));
    	$this->assertEquals($this->service->getTranslationFileInfos('other-domain','fr_FR'),array(
			'pathname' => getcwd().'/TranslatorToolsTest/_files/other-domain/french-file.php',
			'type' => 'array'
		));
    	$this->assertEquals($this->service->getTranslationFileInfos('other-domain','en_US'),array(
			'pathname' => getcwd().'/TranslatorToolsTest/_files/other-domain/english-file.php',
			'type' => 'array'
		));
    }

    public function testGetAvailableMessages(){
    	$this->assertEquals($this->service->getAvailableMessages('default'),array(
			'default_sample',
    		'french_only_default_sample',
    		'english_only_default_sample',
    	));

    	$this->assertEquals($this->service->getAvailableMessages('other-domain'),array(
    		'other-domain_sample',
    		'french_only_other-domain_sample',
    		'english_only_other-domain_sample'
    	));
    }

    public function testGetMissingMessages(){
    	$this->assertEquals($this->service->getMissingMessages('default'),array(
    		'fr_FR' => array('english_only_default_sample'),
    		'en_US' => array('french_only_default_sample')
    	));

    	$this->assertEquals($this->service->getMissingMessages('other-domain'),array(
    		'fr_FR' => array('english_only_other-domain_sample'),
    		'en_US' => array('french_only_other-domain_sample')
    	));
    }
}