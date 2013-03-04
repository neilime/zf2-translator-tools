<?php
namespace TranslatorTools\I18n\Translator;
class Translator extends \Zend\I18n\Translator\Translator{

	/**
	 * Retrieve available messages
	 * @param string $sLocale
	 * @param string $sTextDomain
	 * @return array
	 */
	public function getMessages($sLocale = null,$sTextDomain = 'default'){
		$sLocale = $sLocale?:$this->getLocale();
		if(!isset($this->messages[$sTextDomain][$sLocale]))$this->loadMessages($sTextDomain, $sLocale);
		if($this->messages[$sTextDomain][$sLocale] instanceof \Zend\I18n\Translator\TextDomain)return $this->messages[$sTextDomain][$sLocale]->getArrayCopy();
		if(null !== ($sFallbackLocale = $this->getFallbackLocale()) && $sLocale !== $sFallbackLocale)$this->loadMessages($sTextDomain, $sFallbackLocale);
		return $this->messages[$sTextDomain][$sFallbackLocale] instanceof \Zend\I18n\Translator\TextDomain?$this->messages[$sTextDomain][$sFallbackLocale]->getArrayCopy():array();
	}

	public function getKnownLocales(){
		$aLocales = array($this->getLocale(),$this->getFallbackLocale());
		//Retrieve locales from files configuration
		if(is_array($this->files))foreach($this->files as $sTextDomain => $aFileInfos){
			$aLocales = array_merge($aLocales,array_keys($aFileInfos));
		}

		//Deduplication
		$aLocales = array_unique($aLocales);

		//Remove "*" locale
		if(($iKey = array_search('*',$aLocales)) !== false)unset($aLocales[$iKey]);
		return $aLocales;
	}

	/**
	 * Retrieve messages from files for a given TextDomain and locale
	 * @param string $sTextDomain
	 * @param string $sLocale
	 * @throws \RuntimeException
	 * @return array
	 */
	public function getMessagesByFiles($sTextDomain, $sLocale){
        $aTranslations = array();

        //Try to load from pattern
        if(isset($this->patterns[$sTextDomain])) {
            foreach ($this->patterns[$sTextDomain] as $aPatternInfos) {
                $sFilename = $aPatternInfos['baseDir'] . '/' . sprintf($aPatternInfos['pattern'], $locale);
                if(is_file($sFilename)){
                    $oLoader = $this->getPluginManager()->get($aPatternInfos['type']);
                    if(!$oLoader instanceof FileLoaderInterface)throw new \RuntimeException('Specified loader is not a file loader');
                    $aTranslations[] = array(
                    	'filename' => $sFilename,
                    	'messages' => $oLoader->load($sLocale, $sFilename)
                    );
                }
            }
        }

        // Try to load from concrete files
        foreach(array($sLocale, '*') as $sCurrentLocale) {
            if(!isset($this->files[$sTextDomain][$sCurrentLocale]))continue;
            foreach($this->files[$sTextDomain][$sCurrentLocale] as $aFileInfos){
                $oLoader = $this->getPluginManager()->get($aFileInfos['type']);
                if(!$oLoader instanceof FileLoaderInterface)throw new \RuntimeException('Specified loader is not a file loader');
                $aTranslations[] = array(
                	'filename' => $aFileInfos['filename'],
                	'messages' => $oLoader->load($sLocale, $aFileInfos['filename'])
                );
            }
        }
	}
}