<?php
namespace TranslatorTools\I18n\Translator;
class Translator extends \Zend\I18n\Translator\Translator{

	/**
	 * @var \TranslatorTools\I18n\Translator\WriterPluginManager
	 */
	protected $writerPluginManager;

	protected $filesCopy;

	/**
	 * Add a translation file.
	 * @param string  $type
	 * @param string $filename
	 * @param string $textDomain
	 * @param string $locale
	 * @return \TranslatorTools\I18n\Translator\Translator
	 */
	public function addTranslationFile($sType,$sFilename,$sTextDomain = 'default',$sLocale = null){
		$sLocale = $sLocale ?: '*';
		if(!isset($this->files[$sTextDomain]))$this->files[$sTextDomain] = array();
		if(!isset($this->filesCopy[$sTextDomain]))$this->filesCopy[$sTextDomain] = array();

		$this->files[$sTextDomain][$sLocale][] = $this->filesCopy[$sTextDomain][$sLocale][] = array(
			'type' => $sType,
			'filename' => $sFilename,
		);
		return $this;
	}

	/**
	 * Retrieve available messages
	 * @param string $sLocale
	 * @param string $sTextDomain
	 * @return array
	 */
	public function getMessages($sLocale = null,$sTextDomain = 'default'){
		$sLocale = $sLocale?:$this->getLocale();
		if(!isset($this->messages[$sTextDomain][$sLocale]))$this->loadMessages($sTextDomain, $sLocale);

		if(isset($this->messages[$sTextDomain][$sLocale]) && $this->messages[$sTextDomain][$sLocale] instanceof \Zend\I18n\Translator\TextDomain)return $this->messages[$sTextDomain][$sLocale]->getArrayCopy();

		if(null !== ($sFallbackLocale = $this->getFallbackLocale()) && $sLocale !== $sFallbackLocale)$this->loadMessages($sTextDomain, $sFallbackLocale);
		return isset($this->messages[$sTextDomain][$sLocale]) && $this->messages[$sTextDomain][$sFallbackLocale] instanceof \Zend\I18n\Translator\TextDomain?$this->messages[$sTextDomain][$sFallbackLocale]->getArrayCopy():array();
	}

	/**
	 * @return array
	 */
	public function getKnownLocales(){
		$aLocales = array($this->getLocale(),$this->getFallbackLocale());
		//Retrieve locales from files configuration
		if(is_array($this->filesCopy))foreach($this->filesCopy as $sTextDomain => $aFileInfos){
			$aLocales = array_merge($aLocales,array_keys($aFileInfos));
		}

		//Deduplication
		$aLocales = array_unique($aLocales);

		//Remove "*" locale
		if(($iKey = array_search('*',$aLocales)) !== false)unset($aLocales[$iKey]);
		return array_values(array_filter($aLocales));
	}

	/**
	 * @return array
	 */
	public function getKnownTextDomains(){
		$aTextDomains = array('default');
		//Retrieve text domains from files configuration
		if(is_array($this->filesCopy))$aTextDomains = array_merge($aTextDomains,array_keys($this->filesCopy));

		//Retrieve text domains from patterns configuration
		if(is_array($this->patterns))$aTextDomains = array_merge($aTextDomains,array_keys($this->patterns));

		return array_values(array_filter(array_unique($aTextDomains)));
	}

	/**
	 * @param string $sLocale
	 * @param string $sTextDomain
	 * @throws \LogicException
	 * @return array
	 */
	public function getTranslationFileInfos($sLocale = null,$sTextDomain = 'default'){

		$sLocale = $sLocale?:$this->getLocale();

		if(isset($this->filesCopy[$sTextDomain][$sLocale][0])){
			$aFileInfos = $this->filesCopy[$sTextDomain][$sLocale][0];
			if(
				!isset($aFileInfos['filename'])
				|| !isset($aFileInfos['type'])
			)throw new \LogicException(sprintf(
				'File configuration for locale "%s" and domain "%s" is not valid : %s',
				$sTextDomain,$sLocale,print_r($aFileInfos,true)
			));
			$aFileInfos['filename'] = realpath($aFileInfos['filename']);
		}
		elseif(isset($this->patterns[$sTextDomain][0])){
			if(
				!isset($this->patterns[$sTextDomain][0]['baseDir'])
				|| !isset($this->patterns[$sTextDomain][0]['pattern'])
				|| !isset($this->patterns[$sTextDomain][0]['type'])
			)throw new \LogicException(sprintf(
				'Pattern configuration for locale "%s" and domain "%s" is not valid : %s',
				$sTextDomain,$sLocale,print_r($this->patterns[$sTextDomain][0],true)
			));

			$aFileInfos = array(
				'filename' => $this->patterns[$sTextDomain][0]['baseDir'].DIRECTORY_SEPARATOR.sprintf($this->patterns[$sTextDomain][0]['pattern'], $sLocale),
				'type' => $this->patterns[$sTextDomain][0]['type']
			);
			$aFileInfos['filename'] = file_exists($aFileInfos['filename'])
				?realpath($aFileInfos['filename'])
				:str_ireplace(array('/','\\'), DIRECTORY_SEPARATOR, $aFileInfos['filename']);
		}
		else throw new \LogicException(sprintf(
			'Unable to retrieve translation file infos for locale "%s" and domain "%s"',
			$sLocale,$sTextDomain
		));
		return $aFileInfos;
	}

	/**
	 * @param array $aMessages
	 * @param string $sLocale
	 * @param string $sTextDomain
	 * @throws \RuntimeException
	 * @return \TranslatorTools\I18n\Translator\Translator
	 */
	public function writeTranslations($aMessages,$sLocale = null,$sTextDomain = 'default'){
		$sLocale = $sLocale?:$this->getLocale();
		$aTranslationFileInfos = $this->getTranslationFileInfos($sLocale,$sTextDomain);
		$oWriter = $this->getWriterPluginManager()->get($aTranslationFileInfos['type']);

		if(!$oWriter instanceof \TranslatorTools\I18n\Translator\Writer\FileWriterInterface)throw new \RuntimeException('Specified writer is not a file writer');
		$oWriter->write($aMessages, $aTranslationFileInfos['filename']);
		return $this;
	}

	/**
	 * Retrieve the plugin manager for translation writers.
	 * Lazy loads an instance if none currently set.
	 * @return \TranslatorTools\I18n\Translator\WriterPluginManager
	 */
	public function getWriterPluginManager(){
		if(!$this->writerPluginManager instanceof \TranslatorTools\I18n\Translator\WriterPluginManager)$this->setWriterPluginManager(new WriterPluginManager());
		return $this->writerPluginManager;
	}

	/**
	 * Set the plugin manager for translation writers
	 * @param \TranslatorTools\I18n\Translator\WriterPluginManager $oPluginManager
	 * @return \TranslatorTools\I18n\Translator\Translator
	 */
	public function setWriterPluginManager(\TranslatorTools\I18n\Translator\WriterPluginManager $oPluginManager){
		$this->writerPluginManager = $oPluginManager;
		return $this;
	}
}