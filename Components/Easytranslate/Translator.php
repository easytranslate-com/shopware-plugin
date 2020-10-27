<?php

namespace Easytranslate\Components\Easytranslate;

use Easytranslate\Components\Easytranslate\Models\Project;
use Easytranslate\Components\Easytranslate\Models\Task;
use Easytranslate\Components\Easytranslate\Models\TaskLog;
use Exception;

/**
 * Class Translator
 * @package Easytranslate\Components\Easytranslate
 */
class Translator
{

    /**
     * @param array $identifiers
     * @param string $objectType
     * @param array $fieldsOfInterest
     * @param Language $sourceLanguage
     * @param array $targetLanguages
     * @param string $projectName
     * @return Project
     * @throws Exception
     */
    public static function translate(array $identifiers, string $objectType, array $fieldsOfInterest,
                                     Language $sourceLanguage, array $targetLanguages, string $projectName)
    {
        $apiService = Config::getApiService();

        $contentCollection = array();
        foreach ($identifiers as $identifier) {
            $className = Config::getTranslatableEntities()[$objectType];
            $translatable = new $className();
            $contentCollection[$identifier] = $translatable->getContent($identifier, $fieldsOfInterest, $sourceLanguage);
        }

        $callbackToken = bin2hex(openssl_random_pseudo_bytes(32));

        $projectData = $apiService->createNewProject(
            $sourceLanguage->getEasytranslateLanguage(),
            array_map(function(Language $lang) {
                return $lang->getEasytranslateLanguage();
            }, $targetLanguages),
            $contentCollection,
            EasytranslateApiHelper::getCallbackUrl($callbackToken),
            $projectName
        );

        // handle API error
        if (isset($projectData['errors']) && count($projectData['errors'])) {
            $errors = [];
            foreach ($projectData['errors'] as $k => $v) {
                $errors[] = $k . ': ' . join(", ", $v);
            }
            throw new Exception(join(', ', $errors));
        }

        $project = new Project(
            $projectData['data']['id'],
            $projectData['data']['attributes']['name'],
            $objectType,
            json_encode($fieldsOfInterest),
            $callbackToken
        );
        Config::getProjectRepository()->save($project);

        foreach ($projectData['included'] as $task) {
            if ($task['type'] !== 'task') continue;

            $taskId = $task['id'];
            $targetLocale = $task['attributes']['target_language'];
            $status = $task['attributes']['status'];
            $price = $task['attributes']['price'];

            $matchingTargetLocales = array_values(
                array_filter($targetLanguages, function(Language $lang) use ($targetLocale) {
                    return $lang->getEasytranslateLanguage() == $targetLocale;
                })
            );

            if (count($matchingTargetLocales) == 0) {
                throw new Exception("No matching target locales found");
            }

            $newTask = new Task($taskId, $sourceLanguage, $matchingTargetLocales[0], $project, $status, json_encode($price));
            Config::getTaskRepository()->save($newTask);

            $newTaskLog = new TaskLog($newTask, json_encode($task));
            Config::getTaskLogRepository()->save($newTaskLog);
        }

        return $project;
    }

    /**
     * @param $taskData
     * @param $taskTranslatedContent
     * @throws Exception
     */
    public static function handleTaskUpdated($taskData, $taskTranslatedContent) {

        $task = self::updateTask($taskData['data']);

        $newTaskLog = new TaskLog($task, json_encode($taskData['data']));
        Config::getTaskLogRepository()->save($newTaskLog);

        foreach ($taskData['included'] as $projectData) {
            if ($projectData['type'] === 'project') {
                $project = self::updateProject($projectData);
            }
        }

        if (!$project) {
            throw new Exception("No project data in included part of API response");
        }

        $type = $project->getObjectType();
        $fieldsOfInterest = $project->getFieldsOfInterest();

        foreach($taskTranslatedContent as $id => $content) {
            $className = Config::getTranslatableEntities()[$type];
            /**
             * @var Translatable $object
             */
            $object = new $className();
            $object->setContent($taskData['data']['id'], $id, $content, $fieldsOfInterest, $task);
        }
    }

    /**
     * @param $projectData
     */
    public static function handleApprovalNeeded($projectData) {

        self::updateProject($projectData['data']);

        foreach ($projectData['included'] as $taskData) {
            if ($taskData['type'] !== 'task') {
                continue;
            }

            $task = self::updateTask($taskData);

            $newTaskLog = new TaskLog($task, json_encode($taskData));
            Config::getTaskLogRepository()->save($newTaskLog);
        }
    }

    /**
     * Helper function
     * @param $taskData
     * @return Task
     */
    public static function updateTask($taskData) {
        $taskId = $taskData['id'];
        $taskPrice = $taskData['attributes']['price'];
        $taskStatus = $taskData['attributes']['status'];

        /** @var Task $task */
        $task = Config::getTaskRepository()->load($taskId);
        $task->setStatus($taskStatus);
        $task->setPrice(json_encode($taskPrice));
        Config::getTaskRepository()->update($task);

        return $task;
    }

    /**
     * Helper function
     * @param $projectData
     * @return Project
     */
    public static function updateProject($projectData) {
        $projectId = $projectData['id'];
        $projectStatus = $projectData['attributes']['status'];
        $projectPrice = $projectData['attributes']['price'];

        /** @var Project $project */
        $project = Config::getProjectRepository()->load($projectId);
        $project->setStatus($projectStatus);
        $project->setPrice(json_encode($projectPrice));
        Config::getProjectRepository()->update($project);

        return $project;
    }
}
