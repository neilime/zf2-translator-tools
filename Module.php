<?php
namespace TranslatorTools;
class Module implements
	\Zend\ModuleManager\Feature\ConfigProviderInterface,
	\Zend\ModuleManager\Feature\AutoloaderProviderInterface,
	\Zend\ModuleManager\Feature\ConsoleUsageProviderInterface{

	/**
	 * @param \Zend\Console\Adapter\AdapterInterface $oConsole
	 * @return string
	 */
	public function getConsoleBanner(\Zend\Console\Adapter\AdapterInterface $oConsole){
		return 'TranslatorTools - Command line Tool';
	}

	/**
	 * @see \Zend\ModuleManager\Feature\ConsoleUsageProviderInterface::getConsoleUsage()
	 * @param \Zend\Console\Adapter\AdapterInterface $oConsole
	 * @return array
	 */
	public function getConsoleUsage(\Zend\Console\Adapter\AdapterInterface $oConsole){
		return array(
			'Remove translations:',
			'removeTranslations' => 'remove useless translations',
		);
	}

	/**
	 * @see \Zend\ModuleManager\Feature\AutoloaderProviderInterface::getAutoloaderConfig()
	 * @return array
	 */
	public function getAutoloaderConfig(){
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php'
            )
        );
    }

    /**
     * @return array
     */
    public function getConfig(){
        return include __DIR__ . '/config/module.config.php';
    }
}