<?php
namespace TranslatorTools\I18n\Translator\Writer;
class Ini implements \TranslatorTools\I18n\Translator\Writer\FileWriterInterface{

	/**
	 * @see \TranslatorTools\I18n\Translator\Writer\FileWriterInterface::write()
	 * @param array $aMessages
	 * @param string $sFilename
	 * @throws \InvalidArgumentException
	 * @return \TranslatorTools\I18n\Translator\Writer\Ini
	 */
	public function write(array $aMessages, $sFilename){

		$aTempMessages = array();
		foreach($aMessages as $sMessage => $sTranslation){
			$aTempMessages[str_replace('"','\'', $sMessage)] = str_replace('"','\'', $sTranslation);
		}
		$aMessages = $aTempMessages;

		if(is_file($sFilename)){
			if(!is_readable($sFilename))throw new \InvalidArgumentException(sprintf(
				'Could not open file "%s" for writing',
				$sFilename
			));

	        $oIniReader = new \Zend\Config\Reader\Ini();
	        $aMessagesNamespaced = $oIniReader->fromFile($sFilename);
	        $aListMessages = isset($aMessagesNamespaced['translation'])?$aMessagesNamespaced['translation']:$aMessagesNamespaced;

	        foreach($aListMessages as $aMessage){
	            if(!is_array($aMessage) || count($aMessage) < 2){
	        		/* TODO remove */error_log('$aMessage : '.print_r($aMessage,true));
	            	throw new \InvalidArgumentException('Each INI row must be an array with message and translation');
	            }
	            if(isset($aMessage['message']) && isset($aMessage['translation'])){
					//Add message only if not given
	            	if(!isset($aMessages[$aMessage['message']]))$aMessages[$aMessage['message']] = $aMessage['translation'];
	                continue;
	            }
	            //Add message only if not given
	            $sMessage = array_shift($aMessage);
	            if(!isset($aMessages[$sMessage]))$aMessages[$sMessage] = array_shift($aMessage);
	        }
		}
		//Sort messages
		else $aMessagesNamespaced = null;

		//Sort messages
		ksort($aMessages);

		$aMessagesKeys = array_keys($aMessages);
		$aMessages = array_combine($aMessagesKeys,array_map(function($sTranslation,$sMessage){
			return array('message'=>$sMessage,'translation' => $sTranslation);
		},$aMessages,$aMessagesKeys));

		if(isset($aMessagesNamespaced['translation']))$aMessagesNamespaced['translation'] = $aMessages;
		else $aMessagesNamespaced = $aMessages;

        $oIniWriter = new \Zend\Config\Writer\Ini();
        $oIniWriter->toFile($sFilename, $aMessagesNamespaced);
        return $this;
    }
}