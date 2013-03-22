<?php
namespace TranslatorTools\I18n\Translator\Writer;
interface FileWriterInterface{
    /**
     * Write a translation in a file.
     * @param array $aMessages
     * @param string $sFilename
     * @return \TranslatorTools\I18n\Translator\Writer\FileWriterInterface
     */
    public function write(array $aMessages, $sFilename);
}