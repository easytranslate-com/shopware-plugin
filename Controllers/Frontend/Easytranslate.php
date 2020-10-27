<?php

use Doctrine\ORM\EntityManager;
use Easytranslate\Components\Easytranslate\Config;
use Easytranslate\Components\Easytranslate\Translator;
use Easytranslate\Models\Project;
use Shopware\Components\CSRFWhitelistAware;

class Shopware_Controllers_Frontend_Easytranslate extends Enlight_Controller_Action implements CSRFWhitelistAware
{
    public function getWhitelistedCSRFActions()
    {
        // Whitelist the action
        return ['webhook'];
    }

    public function webhookAction()
    {
        $this->container->get('events')->notify('Webhook_Event');

        $callbackToken = $this->Request()->getParam("token");

        /** @var EntityManager $em */
        $em = $this->container->get('models');

        $request = $this->Request();
        $content = json_decode($request->getContent(), true);

        if ($content['event'] === 'task.updated') {
            $taskData = $content['data'];
            $taskId = $taskData['id'];
            $projectId = $taskData['attributes']['project_id'];


            /** @var Project $project */
            $project = $em->getRepository(Project::class)
                ->findOneBy(["projectId" => $projectId]);

            $projectCallbackToken = $project->getCallbackToken();

            if ($projectCallbackToken !== $callbackToken) {
                var_dump('callbackToken does not match!');
                die();
            }

            $apiService = Config::getApiService();

            $taskTranslatedContent = $apiService->getContentForTask($projectId, $taskId);

            Translator::handleTaskUpdated($content, $taskTranslatedContent);
        }
        elseif ($content['event'] === 'project.status.approval_needed') {

            $projectId = $content['data']['id'];

            /** @var Project $project */
            $project = $em->getRepository(Project::class)
                ->findOneBy(["projectId" => $projectId]);

            $projectCallbackToken = $project->getCallbackToken();

            if ($projectCallbackToken !== $callbackToken) {
                var_dump('callbackToken does not match!');
                die();
            }

            Translator::handleApprovalNeeded($content);
        }
    }
}
