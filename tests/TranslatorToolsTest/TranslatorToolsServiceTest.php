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
    	$this->assertEquals($this->service->getTextDomains(),array('default','other-domain','ini-domain'));
    }

    public function testGetTranslationFileInfos(){
    	$this->assertEquals($this->service->getTranslationFileInfos('fr_FR','default'),array(
			'filename' => realpath(getcwd().'/TranslatorToolsTest/_files/translations/default/fr_FR.php'),
			'type' => 'phparray'
		));
    	$this->assertEquals($this->service->getTranslationFileInfos('en_US','default'),array(
			'filename' =>  realpath(getcwd().'/TranslatorToolsTest/_files/translations/default/en_US.php'),
			'type' => 'phparray'
		));
    	$this->assertEquals($this->service->getTranslationFileInfos('fr_FR','other-domain'),array(
			'filename' =>  realpath(getcwd().'/TranslatorToolsTest/_files/translations/other-domain/french-file.php'),
			'type' => 'phparray'
		));
    	$this->assertEquals($this->service->getTranslationFileInfos('en_US','other-domain'),array(
			'filename' =>  realpath(getcwd().'/TranslatorToolsTest/_files/translations/other-domain/english-file.php'),
			'type' => 'phparray'
		));
    	$this->assertEquals($this->service->getTranslationFileInfos('fr_FR','ini-domain'),array(
    		'filename' =>  realpath(getcwd().'/TranslatorToolsTest/_files/translations/ini-domain/french-file.ini'),
    		'type' => 'ini'
    	));
    	$this->assertEquals($this->service->getTranslationFileInfos('en_US','ini-domain'),array(
    		'filename' =>  realpath(getcwd().'/TranslatorToolsTest/_files/translations/ini-domain/english-file.ini'),
    		'type' => 'ini'
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

    	$this->assertEquals($this->service->getAvailableMessages('ini-domain'),array(
    		'ini-domain_sample',
    		'french_only_ini-domain_sample',
    		'english_only_ini-domain_sample'
    	));
    }

    public function testGetMissingMessages(){
    	$this->assertEquals($this->service->getMissingMessages(null,'default'),array(
    		'fr_FR' => array('english_only_default_sample'),
    		'en_US' => array('french_only_default_sample')
    	));

    	$this->assertEquals($this->service->getMissingMessages(null,'other-domain'),array(
    		'fr_FR' => array('english_only_other-domain_sample'),
    		'en_US' => array('french_only_other-domain_sample')
    	));

    	$this->assertEquals($this->service->getMissingMessages(null,'ini-domain'),array(
    		'fr_FR' => array('english_only_ini-domain_sample'),
    		'en_US' => array('french_only_ini-domain_sample')
    	));
    }

    public function testWriteTranslations(){
    	$this->service->writeTranslations(array(
    		'english_only_default_sample' => 'Exemple "default" seulement en anglais'
    	),'fr_FR','default');

    	$this->assertEquals(
    		file_get_contents(getcwd().'/TranslatorToolsTest/_files/expected/default/fr_FR.php'),
    		file_get_contents(getcwd().'/TranslatorToolsTest/_files/translations/default/fr_FR.php')
    	);

    	$this->service->writeTranslations(array(
    		'english_only_ini-domain_sample' => 'Exemple "ini-domain" seulement en anglais'
    	),'fr_FR','ini-domain');

    	$this->assertEquals(
    		file_get_contents(getcwd().'/TranslatorToolsTest/_files/expected/ini-domain/french-file.ini'),
    		file_get_contents(getcwd().'/TranslatorToolsTest/_files/translations/ini-domain/french-file.ini')
    	);

    	//Copy translation files
    	\TranslatorToolsTest\Bootstrap::rcopy(__DIR__.'/_files/original', __DIR__.'/_files/translations');
    }
}