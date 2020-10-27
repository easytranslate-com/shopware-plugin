<?php

use Easytranslate\Components\Easytranslate\Config;
use Easytranslate\Components\Easytranslate\Models\TaskLog;
use Easytranslate\Components\Easytranslate\Translatable;
use Easytranslate\Components\Easytranslate\Translator;
use Easytranslate\Models\Project;

/**
 * Backend controllers extending from Shopware_Controllers_Backend_Application do support the new backend components
 */
class Shopware_Controllers_Backend_Easytranslate extends Shopware_Controllers_Backend_Application
{
    protected $model = Project::class;
    protected $alias = 'project';

    public function init() {
        parent::init();

        $this->setManager(Shopware()->Models());
    }

    protected function getListQuery()
    {
        $builder = parent::getListQuery();

        return $builder;
    }

    protected function getDetailQuery($id)
    {
        return parent::getDetailQuery($id);
    }

    protected function getAdditionalDetailData(array $data)
    {
        $data['tasks'] = array();
        return $data;
    }

    public function acceptOrDeclineProjectPriceAction() {
        $projectId = $this->Request()->getParam('projectId');
        $acceptOrDecline = $this->Request()->getParam('acceptOrDecline');

        $apiService = Config::getApiService();
        if ($acceptOrDecline === 'accept') {
            $apiService->acceptPriceForProject($projectId);
        }
        elseif ($acceptOrDecline === 'decline') {
            $apiService->declinePriceForProject($projectId);
        }

        $projectData = $apiService->getProject($projectId);

        Translator::updateProject($projectData['data']);

        foreach ($projectData['included'] as $taskData) {
            if ($taskData['type'] !== 'task') continue;

            $task = Translator::updateTask($taskData);

            $newTaskLog = new TaskLog($task, json_encode($taskData));
            Config::getTaskLogRepository()->save($newTaskLog);
        }

        $this->View()->assign([
            'success' => true,
            'data' => [],
        ]);
    }

    public function fetchProjectsAction() {
        $apiService = Config::getApiService();

        $projectIdsInDB = $this->manager->getConnection()->query(
            'SELECT project_id FROM s_plugin_translation_project;'
        )->fetchAll(PDO::FETCH_COLUMN);

        $response = $apiService->getProjects();
        foreach ($response['data'] as $project) {
            if (!in_array($project['id'], $projectIdsInDB)) continue;

            // additional API call necessary to get tasks as well.
            $projectData = $apiService->getProject($project['id']);

            Translator::updateProject($projectData['data']);

            foreach ($projectData['included'] as $taskData) {
                if ($taskData['type'] !== 'task') continue;

                $task = Translator::updateTask($taskData);

                $newTaskLog = new TaskLog($task, json_encode($taskData));
                Config::getTaskLogRepository()->save($newTaskLog);
            }
        }

        $this->View()->assign([
            'success' => true,
            'data' => [],
        ]);
    }

    public function fetchTranslatedContentAction() {
        $taskIds = $this->Request()->getParam('taskId');
        $apiService = Config::getApiService();

        foreach ($taskIds as $taskId) {
            $task = Config::getTaskRepository()->load($taskId);

            $project = $task->getProject();

            $taskTranslatedContent = $apiService->getContentForTask($project->getProjectId(), $taskId);

            $type = $project->getObjectType();
            $fieldsOfInterest = $project->getFieldsOfInterest();

            foreach($taskTranslatedContent as $id => $content) {
                $className = Config::getTranslatableEntities()[$type];
                /**
                 * @var Translatable $object
                 */
                $translatable = new $className();
                $translatable->setContent($taskId, $id, $content, $fieldsOfInterest, $task);
            }
        }

        $this->View()->assign([
            'success' => true,
            'data' => [],
        ]);
    }
}
