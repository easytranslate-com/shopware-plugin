<?php

namespace Easytranslate\Components;

use Easytranslate\Components\Easytranslate\Easytranslate;
use Easytranslate\Components\Easytranslate\EasytranslateApi;
use Easytranslate\Components\Easytranslate\Repository;
use Easytranslate\Translatables\ArticleTranslatable;
use Easytranslate\Translatables\CategoryTranslatable;
use Easytranslate\Translatables\EmotionTranslatable;
use Easytranslate\Translatables\PropertyOptionTranslatable;
use Easytranslate\Translatables\SnippetTranslatable;
use Exception;
use Shopware\Models\Shop\Shop;

/**
 * Class EasytranslateImpl
 * @package Easytranslate\Components
 */
class EasytranslateImpl implements Easytranslate
{
    /**
     * @var mixed|object|\Shopware\Components\DependencyInjection\Container|null
     */
    protected $projectRepository;
    /**
     * @var mixed|object|\Shopware\Components\DependencyInjection\Container|null
     */
    protected $taskRepository;
    /**
     * @var mixed|object|\Shopware\Components\DependencyInjection\Container|null
     */
    protected $taskLogRepository;

    /**
     * EasytranslateImpl constructor.
     */
    public function __construct()
    {
        $container = Shopware()->Container();
        $this->projectRepository = $container->get('easytranslate.project_repository');
        $this->taskRepository = $container->get('easytranslate.task_repository');
        $this->taskLogRepository = $container->get('easytranslate.task_log_repository');
    }

    /**
     * @return Repository
     */
    function getProjectRepository(): Repository
    {
        return $this->projectRepository;
    }

    /**
     * @return Repository
     */
    function getTaskRepository(): Repository
    {
        return $this->taskRepository;
    }

    /**
     * @return Repository
     */
    function getTaskLogRepository(): Repository
    {
        return $this->taskLogRepository;
    }

    /**
     * @return array
     */
    function getTranslatableEntities(): array
    {
        return [
            'article' => new ArticleTranslatable(),
            'category' => new CategoryTranslatable(),
            'snippet' => new SnippetTranslatable(),
            'propertyoption' => new PropertyOptionTranslatable(),
            'emotion' => new EmotionTranslatable(),
        ];
    }

    /**
     * @return EasytranslateApi
     * @throws Exception
     */
    function getApiService(): EasytranslateApi
    {
        $container = Shopware()->Container();
        try {
            $config = $container->get('shopware.plugin.cached_config_reader')->getByPluginName('Easytranslate', Shopware()->Shop());
            $username = $config['Username'];
            $password = $config['Password'];
            $clientId = $config['Client Id'];
            $clientSecret = $config['Client Secret'];
            $sandboxMode = $config['Sandbox Mode'];
        } catch (\Exception $e) {
            $username = $container->get('config')->getByNamespace('Easytranslate', 'Username');
            $password = $container->get('config')->getByNamespace('Easytranslate', 'Password');
            $clientId = $container->get('config')->getByNamespace('Easytranslate', 'Client Id');
            $clientSecret = $container->get('config')->getByNamespace('Easytranslate', 'Client Secret');
            $sandboxMode = $container->get('config')->getByNamespace('Easytranslate', 'Sandbox Mode');
        }

        if (!$username or !$password or !$clientId or !$clientSecret or !isset($sandboxMode)) {
            if (!$username) {
                throw new Exception("Plugin config not loaded properly; username missing");
            }
            if (!$password) {
                throw new Exception("Plugin config not loaded properly; password missing");
            }
            if (!$clientId) {
                throw new Exception("Plugin config not loaded properly; clientId missing");
            }
            if (!$clientSecret) {
                throw new Exception("Plugin config not loaded properly; clientSecret missing");
            }
            if (!isset($sandboxMode)) {
                throw new Exception("Plugin config not loaded properly; sandboxMode missing");
            }
        }


        return new EasytranslateApi($username, $password, $clientId, $clientSecret, $sandboxMode);
    }

    /**
     * @return string
     */
    function getHost(): string {
        try {
            $shop = Shopware()->Shop();
        } catch (\Exception $e) {
            $container = Shopware()->Container();

            /** @var \Doctrine\ORM\EntityManager $em */
            $em = $container->get('models');

            /** @var Shop $shop */
            $shop = $em->getRepository(Shop::class)
                ->findOneBy(["mainId" => null]);
        }

        $protocol = $shop->getSecure() ? "https://" : "http://";
        return $protocol . $shop->getHost();
    }
}
