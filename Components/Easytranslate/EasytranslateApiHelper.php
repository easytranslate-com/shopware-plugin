<?php

namespace Easytranslate\Components\Easytranslate;

/**
 * Class EasytranslateApiHelper
 * @package Easytranslate\Components\Easytranslate
 */
class EasytranslateApiHelper
{
    const CALLBACK_PATH = "/frontend/easytranslate/webhook";

    /**
     * @param $callbackToken
     * @return string
     */
    public static function getCallbackUrl($callbackToken) {
        // for testing on your local machine we recommend ngrok.io
        // return 'https://b46c5444ac4b.ngrok.io' . self::CALLBACK_PATH . '?token=' . $callbackToken;

        return Config::getHost() . self::CALLBACK_PATH . '?token=' . $callbackToken;
    }
}
