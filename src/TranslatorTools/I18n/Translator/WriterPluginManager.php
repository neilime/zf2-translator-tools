<?php
namespace TranslatorTools\I18n\Translator;
class WriterPluginManager extends \Zend\ServiceManager\AbstractPluginManager{
    /**
     * Default set of writers.
     * @var array
     */
    protected $invokableClasses = array(
        'ini' => 'TranslatorTools\I18n\Translator\Writer\Ini',
        'phparray' => 'TranslatorTools\I18n\Translator\Writer\PhpArray'
    );

    /**
     * @param mixed $oPlugin
     * @return void
     * @throws \RuntimeException if invalid
     */
    public function validatePlugin($oPlugin){
        if($oPlugin instanceof \TranslatorTools\I18n\Translator\Writer\FileWriterInterface)return;
        throw new Exception\RuntimeException(sprintf(
            'Plugin of type "%s" is invalid; must implement \TranslatorTools\I18n\Translator\Writer\FileWriterInterface',
            is_object($oPlugin)?get_class($oPlugin):gettype($oPlugin)
        ));
    }
}