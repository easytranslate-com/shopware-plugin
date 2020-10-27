<?php

namespace Easytranslate\Components\Easytranslate;

/**
 * Interface Easytranslate
 * @package Easytranslate\Components\Easytranslate
 */
interface Easytranslate
{
    function getProjectRepository(): Repository;
    function getTaskRepository(): Repository;
    function getTaskLogRepository(): Repository;
    function getTranslatableEntities(): array;
    function getApiService(): EasytranslateApi;
}
