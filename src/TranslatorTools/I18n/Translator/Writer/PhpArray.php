<?php
namespace TranslatorTools\I18n\Translator\Writer;
class PhpArray implements \TranslatorTools\I18n\Translator\Writer\FileWriterInterface{
	/**
	 * @see \TranslatorTools\I18n\Translator\Writer\FileWriterInterface::write()
	 * @param array $aMessages
	 * @param string $sFilename
	 * @throws \InvalidArgumentException
	 * @throws \RuntimeException
	 * @return \TranslatorTools\I18n\Translator\Writer\PhpArray
	 */
    public function write(array $aMessages, $sFilename){
        if(is_file($sFilename)){
        	if(!is_readable($sFilename))throw new \InvalidArgumentException(sprintf(
	        	'Could not open file "%s" for reading',
	        	$sFilename
	        ));
	        $aOldMessages = include $sFilename;
	        if(!is_array($aOldMessages))throw new \InvalidArgumentException(sprintf(
	        	'Expected an array, but received "%s"',
	        	gettype($aOldMessages)
	        ));
	        //Merge old and new messages
    	    $aMessages = array_merge($aOldMessages,$aMessages);
        }

        //Sort messages
        ksort($aMessages);

        if(!file_put_contents($sFilename,sprintf(
        	'<?php'.PHP_EOL.'return array('.PHP_EOL.'%s'.PHP_EOL.');',
        	join(','.PHP_EOL,array_map(function($sTranslation,$sMessage){
				return "\t".'\''.addcslashes($sMessage,'\'').'\' => \''.addcslashes($sTranslation,'\'').'\'';

        	},$aMessages,array_keys($aMessages)))
        )))throw new \RuntimeException(sprintf('Could not put contents in file "%s"',$sFilename));
        return $this;
    }
}