<?php

namespace Easytranslate;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context\UpdateContext;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Doctrine\ORM\Tools\SchemaTool;
use Easytranslate\Setup\Updater;

/**
 * Shopware-Plugin Easytranslate.
 */
class Easytranslate extends Plugin
{
    /**
     * Adds the widget to the database and creates the database schema.
     *
     * @param Plugin\Context\InstallContext $installContext
     */
    public function install(Plugin\Context\InstallContext $installContext)
    {
        parent::install($installContext);

        $this->createSchema();

    }

    /**
     * Remove widget and remove database schema.
     *
     * @param Plugin\Context\UninstallContext $uninstallContext
     */
    public function uninstall(Plugin\Context\UninstallContext $uninstallContext)
    {
        parent::uninstall($uninstallContext);

        $this->removeSchema();
    }

    /**
     * @param Shopware\Components\Plugin\Context\UpdateContext $updateContext
     */
    public function update(UpdateContext $context)
    {
        $updater = new Updater(
            $this->container->get('shopware_attribute.crud_service'),
            $this->container->get('models'),
            $this->container->get('dbal_connection')
        );
        $updater->update($context->getCurrentVersion());

        $context->scheduleClearCache(UpdateContext::CACHE_LIST_ALL);
    }

    /**
    * @param ContainerBuilder $container
    */
    public function build(ContainerBuilder $container)
    {
        $container->setParameter('easytranslate.plugin_dir', $this->getPath());
        parent::build($container);
    }

    /**
     * creates database tables on base of doctrine models
     */
    private function createSchema()
    {
        $tool = new SchemaTool($this->container->get('models'));
        $classes = [
            $this->container->get('models')->getClassMetadata(\Easytranslate\Models\Task::class),
            $this->container->get('models')->getClassMetadata(\Easytranslate\Models\Project::class),
            $this->container->get('models')->getClassMetadata(\Easytranslate\Models\Task::class),
            $this->container->get('models')->getClassMetadata(\Easytranslate\Models\TaskLog::class),
            $this->container->get('models')->getClassMetadata(\Easytranslate\Models\TranslationProfile::class)
        ];
        $tool->createSchema($classes);
    }

    private function removeSchema()
    {
        $tool = new SchemaTool($this->container->get('models'));
        $classes = [
            $this->container->get('models')->getClassMetadata(\Easytranslate\Models\Task::class),
            $this->container->get('models')->getClassMetadata(\Easytranslate\Models\TaskLog::class),
            $this->container->get('models')->getClassMetadata(\Easytranslate\Models\Task::class),
            $this->container->get('models')->getClassMetadata(\Easytranslate\Models\Project::class),
            $this->container->get('models')->getClassMetadata(\Easytranslate\Models\TranslationProfile::class)
        ];
        $tool->dropSchema($classes);
    }
}
