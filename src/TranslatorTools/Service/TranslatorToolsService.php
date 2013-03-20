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
		$this->setTranslator(\TranslatorTools\I18n\Translator\Translator::factory($aConfiguration['translator']));

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

	/**
	 * Retrieve all defined messages keys
	 * @param string $sTextDomain
	 * @return array
	 */
	public function getAvailableMessages($sTextDomain){
		$aMessagesKeys = array();
		$oTranslator = $this->getTranslator();
		foreach($this->getLocales() as $sLocale){
			$aMessagesKeys = array_merge($aMessagesKeys,array_keys($oTranslator->getMessages($sLocale,$sTextDomain)));
		}
		return array_values(array_unique($aMessagesKeys));
	}

	/**
	 * Retrieve the missing messages for the given text domain and locale
	 * @param string $sTextDomain
	 * @param array $sLocale
	 * @return array
	 */
	public function getMissingMessages($sTextDomain = null,array $aLocales = null){
		$aMissingMessages = array();
		$aAvailableMessages = $this->getAvailableMessages($sTextDomain);
		$aLocales = $aLocales?:$this->getLocales();
		$oTranslator = $this->getTranslator();

		foreach($aLocales as $sLocale){
			$aMissingMessages[$sLocale] = array_values(array_diff(
				$aAvailableMessages,
				array_keys($oTranslator->getMessages($sLocale,$sTextDomain))
			));
		}
		return $aMissingMessages;
	}

	/**
	 * Retrieve translation file infos for the given text domain and locale
	 * @param string $sTextDomain
	 * @param string $sLocale
	 * @return array
	 */
	public function getTranslationFileInfos($sTextDomain = null,$sLocale = null){
		return $this->getTranslator()->getTranslationFileInfos($sTextDomain,$sLocale);
	}

	/**
	 * Add a translation message for the given text domain and locale
	 * @param string $sTextDomain
	 * @param string $sLocale
	 * @param string $sMessage
	 * @param string $sTranslation
	 * @throws \RuntimeException
	 * @return \TranslatorTools\Service\TranslatorToolsService
	 */
	public function addTranslation($sTextDomain = null,$sLocale = null,$sMessage,$sTranslation){
		$aTranslationFileInfos = $this->getTranslationFileInfos($sTextDomain,$sLocale);

		//Create translation file path tree
		if(!is_dir($sTranslationDir = dirname($sTranslationFile))){
			$sCurrentPath = '';
			foreach(explode(DIRECTORY_SEPARATOR,$sTranslationDir) as $sDirPathPart){
				//Create current directory if it doesn't exist
				if(!is_dir($sCurrentPath = $sCurrentPath.DIRECTORY_SEPARATOR.$sDirPathPart)
				&& !mkdir($sCurrentPath))throw new \RuntimeException('Unable to create directory : '.$sCurrentPath);
			}
		}

		//Add translation entry
		switch($aTranslationFileInfos['type']){
		}

		return $this;
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