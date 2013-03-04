<?php
namespace TranslatorTools\Service;
class TranslatorToolsService{
	/**
	 * @var array
	 */
	protected $configuration;

	/**
	 * @var \TranslatorTools\I18n\Translator
	 */
	protected $translator;

	/**
	 * @var array
	 */
	protected $locales;

	/**
	 * @var array
	 */
	protected $textDomains;

	/**
	 * Constructor
	 * @param array $aConfiguration
	 * @throws \InvalidArgumentException
	 */
	public function __construct(array $aConfiguration){
		//Check configuration entries
		if(!isset($aConfiguration['translator']))throw new \InvalidArgumentException('Error in configuration');

		//Initialize translator
		$this->setTranslator(new \TranslatorTools\I18n\Translator\Translator($aConfiguration['translator']));

		if(isset($aConfiguration['locale'])){
			if(!is_array($aConfiguration['locale']))throw new \InvalidArgumentException(sprintf(
				'"locale" configuration expects array, "%s" given',
				gettype($aConfiguration['locale'])
			));
			else $this->setLocales($aConfiguration['locale']);
		}
		$this->configuration = $aConfiguration;
	}

	/**
	 * @param \TranslatorTools\I18n\Translator\Translator $oTranslator
	 * @return \TranslatorTools\Service\TranslatorToolsService
	 */
	public function setTranslator(\TranslatorTools\I18n\Translator\Translator $oTranslator){
		$this->translator = $oTranslator;
		return $this;
	}

	/**
	 * @throws \LogicException
	 * @return \TranslatorTools\I18n\Translator\Translator
	 */
	public function getTranslator(){
		if(!($this->translator instanceof \TranslatorTools\I18n\Translator\Translator))throw new \LogicException('Translator is undefined');
		return $this->translator;
	}

	/**
	 * @param array $aTextDomains
	 * @throws \InvalidArgumentException
	 * @return \TranslatorTools\Service\TranslatorToolsService
	 */
	public function setTextDomains(array $aTextDomains){
		foreach($aTextDomains as $sTextDomain){
			if(!is_string($sTextDomain))throw new \InvalidArgumentException(sprintf(
				'$sTextDomains should contains strings, "%s" given',
				gettype($sTextDomain)
			));
		}
		$this->textDomains = array_unique($aTextDomains);
		return $this;
	}

	/**
	 * Retrieve defined locales
	 * @throws \LogicException
	 * @return array
	 */
	public function getTextDomains(){
		//Try to guess locales
		if(!is_array($this->textDomains)){
			$aTextDomains = $this->getTranslator()->getKnownTextDomains();
			if(empty($aTextDomains))throw new \LogicException('Unable to retrieve text domains');
			$this->setTextDomains($aTextDomains);
		}
		return $this->textDomains;
	}

	/**
	 * @param array $aLocales
	 * @throws \InvalidArgumentException
	 * @return \TranslatorTools\Service\TranslatorToolsService
	 */
	public function setLocales(array $aLocales){
		foreach($aLocales as $sLocale){
			if(!is_string($sLocale))throw new \InvalidArgumentException(sprintf(
				'$aLocales should contains strings, "%s" given',
				gettype($sLocale)
			));
		}
		$this->locales = array_unique($aLocales);
		return $this;
	}

	/**
	 * Retrieve defined locales
	 * @throws \LogicException
	 * @return array
	 */
	public function getLocales(){
		//Try to guess locales
		if(!is_array($this->locales)){
			$aLocales = $this->getTranslator()->getKnownLocales();
			if(empty($aLocales))throw new \LogicException('Unable to retrieve locales');
			$this->setLocales($aLocales);
		}
		return $this->locales;
	}

	public function getUselessTranslations(){
		$oTranslator = $this->getTranslator();
		$aLocales = $this->getLocales();

		$aUselessTranslations = array();
		$aParsedFiles = array();
		foreach($aFiles = $oTranslator->getFiles($this->getTextDomains(),$this->getLocales()) as $sFilename => $aFileInfos){
			//File has already been parsed
			if(isset($aParsedFiles[$sFilename][$aFileInfos['locale']]))$aUselessTranslations[$aFileInfos['textDomain']][$aFileInfos['locale']][$sFilename] = $aParsedFiles[$sFilename][$aFileInfos['locale']];
			else{
				$oLoader = $this->getPluginManager()->get($aFileInfos['type']);
				if(!$oLoader instanceof FileLoaderInterface)throw new \RuntimeException('Specified loader is not a file loader');
				$aOccurences = array();
				foreach((array)$oLoader->load($aFileInfos['locale'], $sFilename) as $sKey => $sMessage){
					//Search useless messages
					$aOccurences[$sKey] = $this->searchOccurence($sKey);
				}
				$aParsedFiles[$sFilename][$aFileInfos['locale']] = $aOccurences;
			}
		}
	}
}