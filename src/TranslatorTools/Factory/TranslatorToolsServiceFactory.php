<?php
namespace TranslatorTools\Factory;
class TranslatorToolsServiceFactory implements \Zend\ServiceManager\FactoryInterface{

	/**
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 * @throws \UnexpectedValueException
	 * @return \TranslatorTools\Service\Service
	 */
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
		$aConfiguration = $oServiceLocator->get('Config');
		$aTranslatorToolsConfiguration = isset($aConfiguration['translator_tools'])?$aConfiguration['translator_tools']:array();
		if(isset($aConfiguration['translator']))$aTranslatorToolsConfiguration['translator'] = $aConfiguration['translator'];

		return new \TranslatorTools\Service\TranslatorToolsService($aTranslatorToolsConfiguration);
	}
}