<?php

namespace Easytranslate\Components\Easytranslate;

/**
 * Class Language
 * @package Easytranslate\Components\Easytranslate
 */
class Language
{
    /**
     * @var string
     */
    protected $easytranslateLanguage;

    protected $hostLanguage;

    /**
     * Language constructor.
     * @param $easytranslateLanguage
     * @param $hostLanguage
     */
    public function __construct($easytranslateLanguage, $hostLanguage)
    {
        $this->easytranslateLanguage = $easytranslateLanguage;
        $this->hostLanguage = $hostLanguage;
    }

    /**
     * @return mixed
     */
    function getEasytranslateLanguage() {
        return $this->easytranslateLanguage;
    }

    /**
     * @return mixed
     */
    function getHostLanguage() {
        return $this->hostLanguage;
    }
}
