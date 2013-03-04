<?php
namespace TranslatorTools\Controller;
class ToolsController extends \Zend\Mvc\Controller\AbstractActionController{

	public function listLocalesAction(){
		$oServiceLocator = $this->getServiceLocator();

		//Initialize TranslatorTools service
		$oTranslatorToolsService = $oServiceLocator->get('TranslatorToolsService');

		$oConsole = $this->getServiceLocator()->get('console');

		$oConsole->writeLine('');
		$oConsole->writeLine('Defined locales : ', \Zend\Console\ColorInterface::GREEN);
		$oConsole->writeLine('-------------------------', \Zend\Console\ColorInterface::GRAY);
		$oConsole->writeLine('');

		foreach($oTranslatorToolsService->getLocales() as $sLocale){
			$oConsole->write(' * ',\Zend\Console\ColorInterface::GRAY);
			$oConsole->write($sLocale,\Zend\Console\ColorInterface::LIGHT_BLUE);
		}
	}

    /**
     * Remove useless translations
     */
	public function removeTranslationsAction(){
        //Initialize TranslatorTools service
        $oTranslatorToolsService = $this->getServiceLocator()->get('TranslatorToolsService');

        $sLocale = $this->params('locale');
        $sTextDomains = $this->params('text-domains');

        //Retrieve useless translations
        $aUselessTranslations = $oTranslatorToolsService->getUselessTranslations($sLocale,$sTextDomains);

        //If user does not want to check translations to be removed
        if($this->params('unsafe'))$oTranslatorToolsService->removeTranslations($aUselessTranslations);

        //Show useless translations
        else{

        }
    }

    /**
     * @param string $sMessage
     * @return \Zend\View\Model\ConsoleModel
     */
    private function sendError($sMessage){
        $oView = new \Zend\View\Model\ConsoleModel();
        $oView->setErrorLevel(2);
        return $oView->setResult($sMessage.PHP_EOL);
    }
}