<?php
/**
 * @author : Gaellan
 * @link : https://github.com/Gaellan
 */


class Translator
{

    public function __construct(private string $file, private string $lang, private array $translations = [])
    {
        $this->loadTranslations();
    }

    private function loadTranslations() : void
    {
        $filePath = "./translations/{$this->file}_{$this->lang}.json";
        $this->translations = json_decode(file_get_contents($filePath), true);
    }

    public function translate(string $key)
    {
        if(isset($this->translations[$key]))
        {
            return $this->translations[$key];
        }
        else
        {
            return $key;
        }
    }
}