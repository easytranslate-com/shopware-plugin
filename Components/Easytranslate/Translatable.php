<?php

namespace Easytranslate\Components\Easytranslate;

use Easytranslate\Components\Easytranslate\Models\Task;

/**
 * Interface Translatable
 * @package Easytranslate\Components\Easytranslate
 */
interface Translatable
{
    public function getType();
    public function getId();

    public function getContent($identifier, $fieldsOfInterest, Language $sourceLanguage);
    public function setContent(string $taskId, string $identifier, array $content, string $fieldsOfInterest, Task $task);
}
