<?php
namespace TranslatorTools\Controller;
class ToolsController extends \Zend\Mvc\Controller\AbstractActionController{
    /**
     * Remove useless translations
     */
	public function removeTranslationsAction(){
        $oServiceLocator = $this->getServiceLocator();
        try{
            $oModuleManager = $oServiceLocator->get('modulemanager');
        }
        catch(\Zend\ServiceManager\Exception\ServiceNotFoundException $oException){
            return $this->sendError('Cannot get Zend\ModuleManager\ModuleManager instance. Is your application using it?');
        }
        $oConsole = $this->getServiceLocator()->get('console');

        //Initialize TranslatorTools service
        $oTranslatorToolsService = $oServiceLocator->get('TranslatorToolsService');

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