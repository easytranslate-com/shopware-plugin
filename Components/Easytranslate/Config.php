<?php

namespace Easytranslate\Components\Easytranslate;

/**
 * Class Config
 * @package Easytranslate\Components\Easytranslate
 */
class Config
{
    /**
     * @var Easytranslate
     */
    private static $dependencies;

    /**
     * @param $deps Easytranslate
     */
    public static function initialize($deps) {
        self::$dependencies = $deps;
    }

    /**
     * @return Repository
     */
    public static function getProjectRepository(): Repository
    {
        return self::$dependencies->getProjectRepository();
    }

    /**
     * @return Repository
     */
    public static function getTaskRepository(): Repository
    {
        return self::$dependencies->getTaskRepository();
    }

    /**
     * @return Repository
     */
    public static function getTaskLogRepository(): Repository
    {
        return self::$dependencies->getTaskLogRepository();
    }

    /**
     * @return array
     */
    public static function getTranslatableEntities(): array
    {
        return self::$dependencies->getTranslatableEntities();
    }

    /**
     * @return EasytranslateApi
     */
    public static function getApiService(): EasytranslateApi
    {
        return self::$dependencies->getApiService();
    }

    /**
     * @return string
     */
    public static function getHost(): string
    {
        return self::$dependencies->getHost();
    }
}
